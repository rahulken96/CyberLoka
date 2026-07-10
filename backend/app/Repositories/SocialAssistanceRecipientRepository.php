<?php

namespace App\Repositories;

use App\Helpers\StringHelper;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use App\Interfaces\SocialAssistanceRecipientRepositoryInterface;
use App\Interfaces\SocialAssistanceRepositoryInterface;
use App\Models\SocialAssistanceRecipient;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SocialAssistanceRecipientRepository implements SocialAssistanceRecipientRepositoryInterface
{
    private HeadOfFamilyRepositoryInterface $headOfFamilyRepoInter;
    private SocialAssistanceRepositoryInterface $socialAssistRepoInter;

    public function __construct(HeadOfFamilyRepositoryInterface $headOfFamilyRepoInter, SocialAssistanceRepositoryInterface $socialAssistRepoInter) {
        $this->headOfFamilyRepoInter = $headOfFamilyRepoInter;
        $this->socialAssistRepoInter = $socialAssistRepoInter;
    }
    
    public function getAll(?string $search, ?int $limit, bool $exec)
    {
        $query = SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily'])
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->search($search);
                }
            });

        $query->orderByDesc('created_at');

        if ($limit) {
            $query->take($limit);
        }

        if ($exec) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginate(?string $search, ?int $rowsPerPage)
    {
        $query = $this->getAll($search, null, false);
        return $query->paginate($rowsPerPage);
    }
    
    public function getOneData(string $string, bool $isWithID = false)
    {
        if ($isWithID) {
            if (!ctype_digit($string)) {
                return null;
            }
            return SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily'])
                ->find($string);
        }

        return SocialAssistanceRecipient::with(['socialAssistance', 'headOfFamily'])
            ->firstWhere('social_recipient_code', $string);
    }

    public function create(array $data)
    {
        $headOfFamilyCode = $data['family_code'];
        $headOfFamily = $this->headOfFamilyRepoInter->getOneData($headOfFamilyCode, false);
        if (!$headOfFamily) {
            throw new Exception('Data Kepala Keluarga tidak ditemukan!', 404);
        }

        $socialCode = $data['social_code'];
        $social = $this->socialAssistRepoInter->getOneData($socialCode, false);
        if (!$social) {
            throw new Exception('Data Bansos tidak ditemukan!', 404);
        }
        if (!$social->is_available) {
            throw new Exception('Data Bansos tidak tersedia!', 404);
        }

        $recipient = new SocialAssistanceRecipient();
        $recipient->social_code       = $socialCode;
        $recipient->family_code       = $headOfFamilyCode;
        $recipient->bank              = $data['bank'];
        $recipient->account_bank      = $data['account_bank'];
        $recipient->amount            = $data['amount'];
        $recipient->reason            = $data['reason'] ?? null;
        $recipient->status            = $data['status'];

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $recipient->image = $data['image']->store('assets/social-assistance-recipient', 'public');
        }
        
        $recipient->save();
        return $recipient;
    }

    public function update(string $string, array $data, bool $isWithID = false)
    {
        $socialAssistRecipient = $this->getOneData($string, $isWithID);
        if (!$socialAssistRecipient) {
            throw new Exception('Data Penerima Bansos tidak ditemukan!', 404);
        }

        if (isset($data['family_code'])){
            $headOfFamilyCode = $data['family_code'];
            $headOfFamily = $this->headOfFamilyRepoInter->getOneData($headOfFamilyCode, false);
            if (!$headOfFamily) {
                throw new Exception('Data Kepala Keluarga tidak ditemukan!', 404);
            }
        }

        if (isset($data['social_code'])){
            $socialCode = $data['social_code'];
            $social = $this->socialAssistRepoInter->getOneData($socialCode, false);
            if (!$social) {
                throw new Exception('Data Bansos tidak ditemukan!', 404);
            }
            if (!$social->is_available) {
                throw new Exception('Data Bansos tidak tersedia!', 404);
            }
        }

        $newImage         = null;
        $oldImageToDelete = null;
        try {
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $newImage = $data['image']->store('assets/social-assistance-recipient', 'public');
                $oldImageToDelete = $socialAssistRecipient->image;
                $socialAssistRecipient->image = $newImage;
            }
            if (isset($data['bank'])) $socialAssistRecipient->bank = $data['bank'];
            if (isset($data['account_bank'])) $socialAssistRecipient->account_bank = $data['account_bank'];
            if (isset($data['amount'])) $socialAssistRecipient->amount = $data['amount'];
            if (isset($data['reason'])) $socialAssistRecipient->reason = $data['reason'];
            if (isset($data['status'])) $socialAssistRecipient->status = $data['status'];
            
            $socialAssistRecipient->save();
            if ($oldImageToDelete && Storage::disk('public')->exists($oldImageToDelete)) {
                Storage::disk('public')->delete($oldImageToDelete);
            }
            return $socialAssistRecipient;
        } catch (\Throwable $err) {
            if ($newImage && Storage::disk('public')->exists($newImage)) {
                Storage::disk('public')->delete($newImage);
            }
            throw $err;
        }
    }

    public function delete(string $string, bool $isWithID = false)
    {
        $socialAssistRecipient = $this->getOneData($string, $isWithID);
        if (!$socialAssistRecipient) {
            throw new Exception('Data Penerima Bansos tidak ditemukan!', 404);
        }
        $socialAssistRecipient->delete();
        return $socialAssistRecipient;
    }
}
