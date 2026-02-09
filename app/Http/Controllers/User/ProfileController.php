<?php

namespace App\Http\Controllers\User;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ProfileRequest;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $states = State::where('is_active', 1)->get();
        return view('user.edit-profile', compact('states'));
    }

    public function update(ProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $user = auth()->user();
            $data = $request->validated();
            $old = $user->toArray();

            // Users cannot update PAN and bank details once entered, UNLESS they were rejected
            // Check if details are rejected (has rejection reason)
            $isRejected = !empty($user->bank_details_rejection_reason);
            
            // PAN card is locked once it has value, unless rejected
            if ($user->pan_card && !$isRejected) {
                unset($data['pan_card']);
            }
            
            // Bank details are locked for users once they have values, unless rejected
            $bankFields = ['account_holder_name', 'bank_name', 'account_number', 'ifsc_code'];
            $bankDetailsChanged = false;
            
            foreach ($bankFields as $field) {
                if ($user->$field && !$isRejected) {
                    // Field already has value and not rejected, user cannot update it
                    unset($data[$field]);
                } elseif (isset($data[$field]) && $data[$field] != $user->$field) {
                    // Field is being changed
                    $bankDetailsChanged = true;
                }
            }
            
            // If PAN is being changed
            if (isset($data['pan_card']) && $data['pan_card'] != $user->pan_card) {
                $bankDetailsChanged = true;
            }
            
            // If any bank details are being changed, set pending approval and clear rejection reason
            if ($bankDetailsChanged) {
                $data['bank_details_approved'] = false;
                $data['bank_details_rejection_reason'] = null;
            }

            // Handle password update
            if (!empty($data['password'])) {
                // Verify current password
                if (!Hash::check($data['current_password'], $user->password)) {
                    return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
                }
                $data['password1'] = EncryptDecrypt("encrypt", $data['password']);
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
                unset($data['password1']);
                unset($data['current_password']);
            }

            // Remove current_password from data
            unset($data['current_password']);

            // Handle profile image upload
            if ($request->hasFile('image')) {
                $oldImage = $user->image;
                $data['image'] = $request->file('image')->store('users', 'public');
                
                // Delete old image if exists
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $user->update($data);
            
            DB::commit();
            Logger::log('update', $user, $old, $user->toArray());
            
            $message = 'Profile updated successfully.';
            if (isset($bankDetailsChanged) && $bankDetailsChanged) {
                $message .= ' Bank details submitted for admin approval. Note: Once submitted, you cannot modify them.';
            }
            
            return redirect()->route('user.profile')->with('success', $message);
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Delete uploaded image if error occurs
            if (isset($data['image']) && $data['image'] != $user->image) {
                Storage::disk('public')->delete($data['image']);
            }
            
            Log::error('[ProfileController@update] QueryException: ' . $e->getMessage(), ['exception' => $e]);
            
            $errorMsg = 'An error occurred while updating your profile.';
            if ($e->getCode() == 23000) {
                $errorMsg = 'Email or mobile number already exists.';
            }
            
            return back()->withErrors(['error' => $errorMsg])->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ProfileController@update] Exception: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['error' => 'Unexpected error: ' . $e->getMessage()])->withInput();
        }
    }
}
