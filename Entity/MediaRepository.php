<?php

namespace Egzakt\MediaBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseEntityRepository;

/**
 * MediaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MediaRepository extends BaseEntityRepository
{
    public function findByType($type)
    {
        $type = ucfirst($type);
        $qb = $this->createQueryBuilder('m')
            ->select('m,mt')
            ->leftJoin('m.thumbnail', 'mt')
            //The discriminator is not an accessible field, we have to use INSTANCE OF
            ->andWhere('m INSTANCE OF :type')
            ->andWhere('m.hidden = false')
            ->setParameter('type', $type);

        return $this->processQuery($qb);
    }
}
