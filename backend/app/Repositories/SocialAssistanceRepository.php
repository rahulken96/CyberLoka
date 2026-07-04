<?php

namespace App\Repositories;

use App\Helpers\StringHelper;
use App\Interfaces\SocialAssistanceRepositoryInterface;
use App\Models\SocialAssistance;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SocialAssistanceRepository implements SocialAssistanceRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $exec)
    {
        $query = SocialAssistance::where(function($q) use ($search) {
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
        $query = $this->getAll($search, $rowsPerPage, false);
        return $query->paginate($rowsPerPage);
    }
    
    public function getOneData(string $string, bool $isWithID = false)
    {
        if ($isWithID) {
            if (!ctype_digit($string)) {
                return null;
            }
            return SocialAssistance::find($string);
        }

        return SocialAssistance::firstWhere('social_code', $string);
    }

    public function create(array $data)
    {
        $socialAssistance = new SocialAssistance();
        $socialAssistance->image        = $data['image']->store('assets/social-assistance', 'public');
        $socialAssistance->name         = $data['name'];
        $socialAssistance->category     = $data['category'];
        $socialAssistance->amount       = $data['amount'];
        $socialAssistance->provider     = $data['provider'];
        $socialAssistance->description  = $data['description'];
        $socialAssistance->is_available = $data['is_available'];
        $socialAssistance->save();
        return $socialAssistance;
    }

    public function update(string $string, array $data, bool $isWithID = false)
    {
        $socialAssistance = $this->getOneData($string, $isWithID);
        if (!$socialAssistance) {
            throw new Exception('Data Bansos tidak ditemukan!', 404);
        }
        $newImage         = null;
        $oldImageToDelete = null;
        try {
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $newImage = $data['image']->store('assets/social-assistance', 'public');
                $oldImageToDelete = $socialAssistance->image;
                $socialAssistance->image = $newImage;
            }
            if (isset($data['name'])) $socialAssistance->name = $data['name'];
            if (isset($data['category'])) $socialAssistance->category = $data['category'];
            if (isset($data['amount'])) $socialAssistance->amount = $data['amount'];
            if (isset($data['provider'])) $socialAssistance->provider = $data['provider'];
            if (isset($data['description'])) $socialAssistance->description = $data['description'];
            if (isset($data['is_available'])) $socialAssistance->is_available = $data['is_available'];
            $socialAssistance->save();
            if ($oldImageToDelete && Storage::disk('public')->exists($oldImageToDelete)) {
                Storage::disk('public')->delete($oldImageToDelete);
            }
            return $socialAssistance;
        } catch (\Throwable $err) {
            if ($newImage && Storage::disk('public')->exists($newImage)) {
                Storage::disk('public')->delete($newImage);
            }
            throw $err;
        }
    }

    public function delete(string $string, bool $isWithID = false)
    {
        $socialAssistance = $this->getOneData($string, $isWithID);
        if (!$socialAssistance) {
            throw new Exception('Data Bansos tidak ditemukan!', 404);
        }
        $socialAssistance->delete();
        return $socialAssistance;
    }
}
