<?php

namespace App\Services;

use App\DataTransferObjects\CreateAttributeData;
use App\DataTransferObjects\CreateHeroData;
use App\Helpers\ServiceHelper;
use App\Http\Resources\Hero\HeroDetailResource;
use App\Http\Resources\Hero\HeroResource;
use App\Repositories\Attribute\AttributeRepositoryInterface;
use App\Repositories\Hero\HeroRepositoryInterface;

class HeroService
{
    protected HeroRepositoryInterface $heroRepository;
    protected AttributeRepositoryInterface $attributeRepository;

    /**
     * @param HeroRepositoryInterface $heroRepository
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        HeroRepositoryInterface $heroRepository,
        AttributeRepositoryInterface $attributeRepository,
    )
    {
        $this->heroRepository = $heroRepository;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Get list hero
     *
     */
    public function getList($params): array
    {
        try {
            $response = $this->heroRepository->listSearch($params);

            return ServiceHelper::paginatedData(HeroResource::collection($response));
        } catch (\Exception $e) {
            logger(__METHOD__ . ' ' . __LINE__ . ': ' . $e->getMessage());
            return ServiceHelper::serverError($e);
        }
    }

    /**
     * Get hero or create if not found
     *
     * @param $id
     * @return array
     */
    public function getHero($id): array
    {
        try {
            $response = $this->heroRepository->find($id);

            return ServiceHelper::data(HeroDetailResource::make($response));
        } catch (\Exception $e) {
            logger(__METHOD__ . ' ' . __LINE__ . ': ' . $e->getMessage());
            return ServiceHelper::serverError($e);
        }
    }

    /**
     * @param CreateAttributeData $attibuteData
     * @param CreateHeroData $heroData
     * @return array
     */
    public function create(CreateAttributeData $attibuteData, CreateHeroData $heroData,): array
    {
        try {
            $attribute = $this->attributeRepository->create($attibuteData->all());
            $response = $this->heroRepository->create(['attribute_id' => $attribute->id, ...$heroData->all()]);

            return ServiceHelper::createdWithData('Hero', $response);
        } catch (\Exception $e) {
            logger(__METHOD__ . ' ' . __LINE__ . ': ' . $e->getMessage());
            return ServiceHelper::serverError($e);
        }
    }

    /**
     * @param $id
     * @param $params
     * @return array
     */
    public function update($id, $params): array
    {
        try {
            $response = $this->heroRepository->updateHero($id, $params);

            return ServiceHelper::updatedWithData('Hero', $response);
        } catch (\Exception $e) {
            logger(__METHOD__ . ' ' . __LINE__ . ': ' . $e->getMessage());
            return ServiceHelper::serverError($e);
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function delete($id): array
    {
        try {
            $response = $this->heroRepository->deleteHero($id);

            if ($response) {
                return ServiceHelper::deleted('Hero');
            } else {
                return ServiceHelper::deleteConflict('Hero');
            }
        } catch (\Exception $e) {
            logger(__METHOD__ . ' ' . __LINE__ . ': ' . $e->getMessage());
            return ServiceHelper::serverError($e);
        }
    }
}
