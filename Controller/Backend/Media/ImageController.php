<?php

namespace Egzakt\MediaBundle\Controller\Backend\Media;

use Doctrine\Tests\ORM\Functional\CompositePrimaryKeyTest;
use Egzakt\MediaBundle\Entity\Image;
use Egzakt\MediaBundle\Entity\Media;
use Egzakt\MediaBundle\Form\ImageType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Egzakt\MediaBundle\Form\MediaType;
use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * ad Controller
 *
 * @throws \Symfony\Bundle\FrameworkBundle\Controller\NotFoundHttpException
 *
 */
class ImageController extends BaseController
{
    /**
     * @var adRepository
     */
    protected $mediaRepository;

    /**
     * Init
     */
    public function init()
    {
        parent::init();
		$this->mediaRepository = $this->mediaRepository = $this->getEm()->getRepository('EgzaktMediaBundle:Image');
    }

	/**
	 * Displays a form to edit an existing ad entity.
	 *
	 * @param integer $id The ad ID
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function editAction(Media $media, Request $request)
	{
		$form = $this->createForm(new ImageType(), $media);

		if("POST" == $request->getMethod()){

			$form->submit($request);

			if($form->isValid()){
				$this->getEm()->persist($media);

				//Update the file only if a new one has been uploaded
				if($media->getMediaFile()){
					$uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
					$uploadableManager->markEntityToUpload($media, $media->getMediaFile());
				}

				$this->getEm()->flush();

				$this->get('egzakt_system.router_invalidator')->invalidate();

				if ($request->request->has('save')) {
					return $this->redirect($this->generateUrl('egzakt_media_backend_media'));
				}

				return $this->redirect($this->generateUrl($media->getRoute(), $media->getRouteParams()));
			}
		}

		return $this->render('EgzaktMediaBundle:Backend/Media/Image:edit.html.twig', array(
			'form' => $form->createView(),
			'media' => $media,
			'isImage' => $media instanceof Image,
		));
	}

	public function updateImageAction($id, Request $request)
	{
		/** @var Image $image */
		$image = $this->mediaRepository->find($id);
		if(!$image){
			return $this->redirect($this->generateUrl('egzakt_media_backend_media'));
		}

		$path = $this->get('kernel')->getRootDir().'/../web'.$image->getMediaPath();
		file_put_contents($path, file_get_contents($request->get('image')));

		$this->getEm()->persist($image);
		$this->getEm()->flush();

		return new JsonResponse(json_encode(array()));
	}

}