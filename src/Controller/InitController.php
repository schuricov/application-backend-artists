<?php

namespace App\Controller;

// Symfony main controller
use App\Entity\Songs;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// Routing
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// Entity
use App\Entity\Albums;
use App\Entity\Artists;

// Model
use App\Models;


class InitController extends Controller
{

    /**
     * @Route("/")
     */

    public function index()
    {
        $description = <<<DESC
<pre>
* For import json data:
- Request
POST {host}/import
- Data
JSON

- Exemple file is:
{host}/src/data/artist-albums.json
</pre>

DESC;

        return new Response($description);
    }

    /**
     * @Route("/import")
     */

    public function import()
    {
        // get json from post request
        $request = Request::createFromGlobals();
        $content = $request->getContent();

        $em = $this->getDoctrine()->getManager();
        $data = new Importer($em);
        $data->import($content);

        return new Response($data);
    }

    /**
     * @Route("/artists/{token}")
     */

    public function artists($token)
    {

        $albumsObj = $this->getDoctrine()->getRepository(Albums::class);
        $artistsObj = $this->getDoctrine()->getRepository(Artists::class);
        $artists = $artistsObj->findOneBy(['token' => $token]);

        if (!$artists) {
            $this->exception($token);
        }

        $return['artist'] = [
                'name' => $artists->getName(),
                'token' => $artists->getToken(),
                'albums' => $albumsObj->findBy(['group_id' => $artists->getGroupId()])
            ];

        return $this->response($return);
    }

    /**
     * @Route("/artists")
     */

    public function artistAll()
    {

        $albumsObj = $this->getDoctrine()->getRepository(Albums::class);
        $artistsObj = $this->getDoctrine()->getRepository(Artists::class);

            $artistsAll = $artistsObj->findAll();

            foreach ($artistsAll as $key => $obj) {
                $return['artists'][$key] = [
                    'token' => $obj->getToken(),
                    'name' => $obj->getName(),
                    'albums' => $albumsObj->findBy(
                        ['group_id' => $obj->getGroupId()]
                    )
                ];
            }

        return $this->response($return);
    }

    /**
     * @Route("/albums/{token}")
     */

    public function Albums($token)
    {

        $albumsObj = $this->getDoctrine()->getRepository(Albums::class);
        $artistsObj = $this->getDoctrine()->getRepository(Artists::class);
        $songsObj = $this->getDoctrine()->getRepository(Songs::class);

        $album = $albumsObj->findOneBy(['token' => $token]);

        if (!$album) {
            $this->exception($token);
        }

        $artists = $artistsObj->findBy(['group_id' => $album->getGroupId()]);
        $songs = $songsObj->findBy(['album_id' => $album->getId()]);

        foreach ($songs as $key => $song){
            $songsArr[$key] = [
                'title' => $song->title,
                'lenth' => str_replace("." ,":", round($song->lenth / 60, 2)),
                ];
        }

        $return = [
            'albums' => [
                'token' => $album->getToken(),
                'tittle' => $album->getTittle(),
                'description' => $album->getDescription(),
                'cover' => $album->getCover(),
                'artists' => $artists,
                'songs' => $songsArr
            ]
        ];

        return $this->response($return);

    }

    public function exception($token = null)
    {

        $code = 404;

        $message = json_encode([
            'token' => $token,
            'message' => 'No data with specified token or data empty...',
            'code' => $code]);

        http_response_code($code);

        exit($message);
    }

    public function response($return){

        if ($return == null or empty($return)){
            $this->exception();
        }

        return new JsonResponse($return);
    }
}



















