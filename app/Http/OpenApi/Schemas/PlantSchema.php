<?php

namespace App\Http\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Plant",
 *     title="Plant",
 *     description="Plant model",
 * )
 */
class PlantSchema {
    /**
     * @OA\Property(property="id", type="integer", format="int64")
     */
    private $id;

    /**
     * @OA\Property(property="perenual_id", type="integer")
     */
    private $perenual_id;

    /**
     * @OA\Property(property="common_name", type="string")
     */
    private $common_name;

    /**
     * @OA\Property(property="scientific_name", type="string", nullable=true)
     */
    private $scientific_name;

    /**
     * @OA\Property(property="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @OA\Property(property="sunlight", type="string", nullable=true)
     */
    private $sunlight;

    /**
     * @OA\Property(property="watering", type="string", nullable=true)
     */
    private $watering;

    /**
     * @OA\Property(
     *     property="watering_general_benchmark",
     *     type="object",
     *     @OA\Property(property="value", type="string"),
     *     @OA\Property(property="unit", type="string")
     * )
     */
    private $watering_general_benchmark;
}

/**
 * @OA\Schema(
 *     schema="CareGuide",
 *     title="Care Guide",
 *     description="Plant care guide"
 * )
 */
class CareGuideSchema {
    /**
     * @OA\Property(property="id", type="integer")
     */
    private $id;

    /**
     * @OA\Property(property="title", type="string")
     */
    private $title;

    /**
     * @OA\Property(property="content", type="string")
     */
    private $content;
}

/**
 * @OA\Schema(
 *     schema="PlantDisease",
 *     title="Plant Disease",
 *     description="Plant disease information"
 * )
 */
class PlantDiseaseSchema {
    /**
     * @OA\Property(property="id", type="integer")
     */
    private $id;

    /**
     * @OA\Property(property="name", type="string")
     */
    private $name;

    /**
     * @OA\Property(property="description", type="string")
     */
    private $description;
}
