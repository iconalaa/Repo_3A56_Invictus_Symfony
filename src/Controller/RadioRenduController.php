<?php

namespace App\Controller;

use App\Entity\CompteRendu;
use App\Form\RadType;
use App\Repository\DoctorRepository;
use App\Repository\ImageRepository;
use App\Repository\RadiologistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class RadioRenduController extends AbstractController
{
    #[Route('/radio', name: 'app_radio', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, DoctorRepository $medrepo, ImageRepository $imagesRepo, RadiologistRepository $repoRad): Response
    {

        // Retrieve the currently logged-in user
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (!$user) {
            throw new \LogicException('No user is logged in.');
        }
        // Retrieve the associated doctor entity using the DoctorRepository
        $radiologist = $repoRad->findOneBy(['user' => $user]);

        // Check if the user is a doctor
        if (!$radiologist) {
            throw new \LogicException('Logged-in user is not associated with any doctor.');
        }
        $compteRendu = new CompteRendu();
        $form = $this->createForm(RadType::class, $compteRendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($compteRendu);
            $entityManager->flush();

            return $this->redirectToRoute('app_image');
        }

        $medecins = $medrepo->findAll();
        $images = $imagesRepo->findImagesWithoutCompteRendu();



        return $this->render('radio/radio.html.twig', [
            'medecins' => $medecins,
            'images' => $images,
            'form' => $form->createView(), // Pass the form to the template
        ]);
    }
}
