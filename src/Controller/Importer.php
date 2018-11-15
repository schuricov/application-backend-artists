<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 11.11.18
 * Time: 20:57
 */

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Groups;
use App\Entity\Artists;
use App\Entity\Albums;
use App\Entity\Songs;

use App\Utils\TokenGenerator;



class Importer

{

    public $data;
    public $status;
    public $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->status = '';
    }

    public function import($data)
    {
//        $this->status = $data;
        $em = $this->em;
        $data = json_decode($data, 1);
//        echo print_r($data, 1); exit;

        foreach ($data as $obj){

            $group = new Groups();
            $group->setName($obj['name']);
            $em->persist($group);
            $em->flush();

            foreach (explode(",", $obj['artists']) as $name){

                $artist = new Artists();
                $artist->setName(trim($name));
                $artist->setGroupId($group->getId());
                $artist->setToken(TokenGenerator::generate(6));
                $em->persist($artist);
                $em->flush();
            }


            if (is_array($obj['albums'])) {

                foreach ($obj['albums'] as $album){

                    $albums = new Albums();
                    $albums->setTittle($album['title']);
                    $albums->setCover($album['cover']);
                    $albums->setDescription($album['description']);
                    $albums->setGroupId($group->getId());
                    $albums->setToken(TokenGenerator::generate(6));

                    $em->persist($albums);
                    $em->flush();

                    if (is_array($album['songs'])){

                        foreach ($album['songs'] as $song){

                            $songs = new Songs();
                            $songs->setTitle($song['title']);
                            $songs->setLenth($this->lenthFormat($song['length']));
                            $songs->setAlbumId($albums->getId());
                            $em->persist($songs);
                            $em->flush();

                        }
                    }
                }
            }
        }

        $this->status = 'Import of data from json has been ok';
    }

    private function lenthFormat($time)
    {
        $time = explode(":", $time);
        $time = $time[0] * 60 + $time[1];

        return $time;
    }

    public function __toString()
    {
        return $this->status;
        // TODO: Implement __toString() method.
    }

}