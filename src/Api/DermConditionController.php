<?php

namespace App\Api; // Sau App\Controller\Api, în funcție de structura ta

use App\Entity\DermCondition;
use App\Repository\DermConditionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/derm/conditions')]
class DermConditionController extends AbstractController
{
    // --- ASIGURĂ-TE CĂ ACEST CONSTRUCTOR NU MAI ESTE COMENTAT ---
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DermConditionRepository $dermConditionRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * Get a paginated list of dermatological conditions.
     */
    #[Route('', name: 'api_derm_conditions_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', 20);
        $searchTerm = $request->query->get('q');
        $tag = $request->query->get('tag');

        $queryBuilder = $this->dermConditionRepository->createQueryBuilder('dc');

        if ($searchTerm) {
            $queryBuilder->andWhere('dc.title LIKE :term OR dc.summary LIKE :term')
                ->setParameter('term', '%' . $searchTerm . '%');
        }

        if ($tag) {
            $queryBuilder->andWhere('dc.tags LIKE :tag')
                ->setParameter('tag', '%\"' . $tag . '\"%');
        }

        $queryBuilder->orderBy('dc.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($queryBuilder);

        $data = [
            'data' => iterator_to_array($paginator->getIterator()),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => count($paginator),
            ]
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'derm_condition:list']);
    }

    /**
     * Get the full details of a single dermatological condition.
     */
    #[Route('/{slug}', name: 'api_derm_conditions_detail', methods: ['GET'])]
    public function detail(DermCondition $dermCondition): JsonResponse
    {
        return $this->json($dermCondition, Response::HTTP_OK, [], ['groups' => 'derm_condition:detail']);
    }

    /**
     * Create a new dermatological condition.
     */
    #[Route('', name: 'api_derm_conditions_create', methods: ['POST'])]
    #[IsGranted('ROLE_EDITOR')]
    public function create(Request $request): JsonResponse
    {
        try {
            /** @var DermCondition $dermCondition */
            $dermCondition = $this->serializer->deserialize(
                $request->getContent(),
                DermCondition::class,
                'json'
            );
        } catch (NotEncodableValueException $e) {
            return $this->json(
                ['errors' => [['message' => 'Invalid JSON payload.']]],
                Response::HTTP_BAD_REQUEST
            );
        }

        $errors = $this->validator->validate($dermCondition);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Presupunem că există o metodă setCreatedBy în entitate
        // $dermCondition->setCreatedBy($this->getUser());

        $this->entityManager->persist($dermCondition);
        $this->entityManager->flush();

        $location = $this->urlGenerator->generate(
            'api_derm_conditions_detail',
            ['slug' => $dermCondition->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(
            $dermCondition,
            Response::HTTP_CREATED,
            ['Location' => $location],
            ['groups' => 'derm_condition:detail']
        );
    }
}
