<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\DocumentVerification;
use App\Models\File;
use App\Models\User;
use DB;

class VerificationController extends Controller
{
    public $successStatus      = 200;
    public $unsuccessStatus    = 403;
    public $unauthorisedStatus = 401;
    protected $helper;

    public function __construct()
    {
        $this->helper          = new Common();
    }

    public function personalId()
    {
        $user_id               = request('user_id');
        $documentVerification  = DocumentVerification::with(['file'])
                                                      ->where(['user_id' => $user_id, 'verification_type' => 'identity'])->first();
        // dd($documentVerification);
        $success['status']     = $this->successStatus;

        return response()->json(['success' => $success, 'documentVerification' => $documentVerification,], $this->successStatus);
    }

    public function updatePersonalId()
    {
        // dd(request()->all());
        try {
            \DB::beginTransaction();

            $user_id                 = request('user_id');
            $user                    = User::find($user_id);
            $user->identity_verified = false;
            $user->save();

            $fileId = $this->insertUserIdentityInfoToFilesTable(request('identity_file'));

            $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'identity'])->first();
            if (empty($documentVerification))
            {
                $createDocumentVerification          = new DocumentVerification();
                $createDocumentVerification->user_id = request('user_id');
                if (!empty(request('identity_file')))
                {
                    $createDocumentVerification->file_id = $fileId;
                }
                $createDocumentVerification->verification_type = 'identity';
                $createDocumentVerification->identity_type     = request('identity_type');
                $createDocumentVerification->identity_number   = request('identity_number');
                $createDocumentVerification->status            = 'pending';
                $createDocumentVerification->save();

                $success['status']   = $this->successStatus;
                $success['message']  = "User Identity Added Successfully";
            }
            else
            {
                $documentVerification->user_id = request('user_id');
                if (!empty(request('identity_file')))
                {
                    $documentVerification->file_id = $fileId;
                }
                $documentVerification->verification_type = 'identity';
                $documentVerification->identity_type     = request('identity_type');
                $documentVerification->identity_number   = request('identity_number');
                $documentVerification->status            = 'pending';
                $documentVerification->save();

                $success['status']   = $this->successStatus;
                $success['message']  = "User Identity Updated Successfully";
            }
         \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $success['status']         = $this->unsuccessStatus;
            $success['exception_msg']  = $e->getMessage();
            $success['message']        = "Sorry, Unexpected error occurred";
        }
        return response()->json(['success' => $success,], $this->successStatus);
    }

    protected function insertUserIdentityInfoToFilesTable($identity_file)
    {
        if (!empty($identity_file))
        {
            if (request()->hasFile('identity_file'))
            {
                $fileName     = request()->file('identity_file');
                $originalName = $fileName->getClientOriginalName();
                $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
                $file_extn    = strtolower($fileName->getClientOriginalExtension());

                if ($file_extn == 'docx' || $file_extn == 'rtf' || $file_extn == 'doc' || $file_extn == 'pdf' || $file_extn == 'png' || $file_extn == 'jpg' || $file_extn == 'jpeg' || $file_extn == 'csv'
                    || $file_extn == 'txt' || $file_extn == 'gif' || $file_extn == 'bmp')
                {
                    $path       = 'uploads/user-documents/identity-proof-files';
                    $uploadPath = public_path($path); //problem
                    $fileName->move($uploadPath, $uniqueName);

                    if (isset(request()->existingIdentityFileID))
                    {
                        // dd($request->existingIdentityFileID);
                        $checkExistingFile               = File::where(['id' => request('existingIdentityFileID')])->first();
                        $checkExistingFile->filename     = $uniqueName;
                        $checkExistingFile->originalname = $originalName;
                        $checkExistingFile->save();
                        return $checkExistingFile->id;
                    }
                    else
                    {
                        $file               = new File();
                        $file->user_id      = request('user_id');
                        $file->filename     = $uniqueName;
                        $file->originalname = $originalName;
                        $file->type         = $file_extn;
                        $file->save();
                        return $file->id;
                    }
                }
            }
        }
    }
    //Personal Identity Verification - end

    //Personal Address Verification - start
    public function personalAddress()
    {
        $user_id               = request('user_id');
        $documentVerification  = DocumentVerification::with(['file'])
                                                      ->where(['user_id' => $user_id, 'verification_type' => 'address'])->first(['file_id']);
        $success['status']     = $this->successStatus;
        return response()->json(['success' => $success, 'documentVerification' => $documentVerification,], $this->successStatus);
    }

    public function updatePersonalAddress()
    {
        // dd(request()->all());
        //make identity_verified false every time a user updates
        try {
            \DB::beginTransaction();
            $user_id                = request('user_id');
            $user                   = User::find($user_id, ['id', 'address_verified']);
            $user->address_verified = false;
            $user->save();

            // $this->validate(request, [
            //     'address_file' => 'mimes:docx,rtf,doc,pdf,png,jpg,jpeg,csv,txt,gif,bmp|max:10000',
            // ]);

            $addressFileId = $this->insertUserAddressProofToFilesTable(request('address_file'));
            // dd($addressFileId);

            $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'address'])->first();
            if (empty($documentVerification))
            {
                $createDocumentVerification          = new DocumentVerification();
                $createDocumentVerification->user_id = $user_id;
                if (!empty(request('address_file')))
                {
                    $createDocumentVerification->file_id = $addressFileId;
                }
                $createDocumentVerification->verification_type = 'address';
                $createDocumentVerification->status            = 'pending';
                $createDocumentVerification->save();

                $success['status']   = $this->successStatus;
                $success['message']  = "User Address Updated Successfully";
            }
            else
            {
                $documentVerification->user_id = $user_id;
                if (!empty(request('address_file')))
                {
                    $documentVerification->file_id = $addressFileId;
                }
                $documentVerification->status = 'pending';
                $documentVerification->save();

                $success['status']   = $this->successStatus;
                $success['message']  = "User Address Updated Successfully";
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $success['status']         = $this->unsuccessStatus;
            $success['exception_msg']  = $e->getMessage();
            $success['message']        = "Sorry, Unexpected error occurred";
        }
        return response()->json(['success' => $success,], $this->successStatus);
    }
    

    protected function insertUserAddressProofToFilesTable($address_file)
    {
        $user_id               = request('user_id');
        if (!empty($address_file))
        {
            if (request()->hasFile('address_file'))
            {
                $fileName     = request()->file('address_file');
                $originalName = $fileName->getClientOriginalName();
                $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
                $file_extn    = strtolower($fileName->getClientOriginalExtension());

                if ($file_extn == 'docx' || $file_extn == 'rtf' || $file_extn == 'doc' || $file_extn == 'pdf' || $file_extn == 'png' || $file_extn == 'jpg' || $file_extn == 'jpeg' || $file_extn == 'csv'
                    || $file_extn == 'txt' || $file_extn == 'gif' || $file_extn == 'bmp')
                {
                    $path       = 'uploads/user-documents/address-proof-files';
                    $uploadPath = public_path($path); //problem
                    $fileName->move($uploadPath, $uniqueName);

                    if (isset(request()->existingAddressFileID))
                    {
                        // dd($request->existingAddressFileID);
                        $checkExistingFile = File::where(['id' => request('existingAddressFileID')])->first();
                        // dd($checkExistingFile);
                        $checkExistingFile->filename     = $uniqueName;
                        $checkExistingFile->originalname = $originalName;
                        $checkExistingFile->save();
                        return $checkExistingFile->id;
                    }
                    else
                    {
                        $file               = new File();
                        $file->user_id      = $user_id;
                        $file->filename     = $uniqueName;
                        $file->originalname = $originalName;
                        $file->type         = $file_extn;
                        $file->save();
                        return $file->id;
                    }
                }
                else
                {
                    $this->helper->one_time_message('error', __('Invalid File Format!'));
                }
            }
        }
    }
    //Personal Address Verification - end
    public function getUserIdentity($user_id)
    {
        $getUserIdentity = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'identity'])->first(['verification_type', 'status']);
        return $getUserIdentity;
    }

    public function getUserAddress($user_id)
    {
        $getUserAddress = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'address'])->first(['verification_type', 'status']);
        return $getUserAddress;
    }
    function checkVerification()
    {
        $user_id               = request('user_id');
        $identityVerification  = $this->getUserIdentity($user_id);
        $addressVerification   = $this->getUserAddress($user_id);
        $success['status']     = $this->successStatus;
        return response()->json(['success' => $success, 
                                 'identityVerification' => $identityVerification,
                                 'addressVerification'  => $addressVerification,], $this->successStatus);
    } 
}
