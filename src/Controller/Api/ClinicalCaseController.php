<?php

namespace App\Controller\Api;

use App\Entity\ClinicalCase;
use App\Entity\Enum\CaseStatus;
use App\Repository\ClinicalCaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/cases')]
class ClinicalCaseController extends AbstractController
{
    #[Route('', name: 'api_cases_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(ClinicalCaseRepository $repository, SerializerInterface $serializer): Response
    {
        $cases = $repository->findBy(['status' => CaseStatus::PUBLISHED]);
        $json = $serializer->serialize($cases, 'json', ['groups' => 'case:read']);
        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'api_cases_get', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getCase(ClinicalCase $case, SerializerInterface $serializer): Response
    {
        if ($case->getStatus() !== CaseStatus::PUBLISHED && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }
        $json = $serializer->serialize($case, 'json', ['groups' => 'case_read_detailed']);
        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('', name: 'api_cases_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, SerializerInterface $serializer): Response
    {
        $case = $serializer->deserialize($request->getContent(), ClinicalCase::class, 'json');
        $case->setCreatedBy($this->getUser());
        $case->addAuditLogEntry('Caz creat.', $this->getUser()->getUserIdentifier());

        $errors = $validator->validate($case);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($case);
        $em->flush();

        return $this->json(['status' => 'Caz creat', 'id' => $case->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_cases_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, ClinicalCase $case, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $serializer->deserialize($request->getContent(), ClinicalCase::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $case
        ]);

        $case->addAuditLogEntry('Cazul a fost actualizat.', $this->getUser()->getUserIdentifier());

        $errors = $validator->validate($case);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return $this->json(['status' => 'Caz actualizat', 'id' => $case->getId()], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_cases_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(ClinicalCase $case, EntityManagerInterface $em): Response
    {
        // Implementăm "soft delete" prin schimbarea statusului, nu prin ștergere fizică
        $case->setStatus(CaseStatus::ARCHIVED);
        $case->addAuditLogEntry('Cazul a fost arhivat (soft delete).', $this->getUser()->getUserIdentifier());

        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
