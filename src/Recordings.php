<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <dkrizan@synopsis.cz>
 * Date: 09.07.20 20:48
 */

namespace App;

use App\Entity\Recording;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class Recordings extends EntityRepository {

    public function __construct(EntityManager $em) {
        parent::__construct($em, new ClassMetadata(Recording::class));
    }
}