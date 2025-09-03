<?php

namespace App\Controller\Api;

use App\Entity\AIConsultation;
use App\Repository\DermConditionRepository;
use App\Repository\MediaAssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/ai')]
class AiController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/chat', name: 'api_ai_chat', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function chat(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $constraints = new Assert\Collection([
            'fields' => [
                'question' => [new Assert\NotBlank(), new Assert\Type('string')],
            ],
            'allowExtraFields' => true,
        ]);

        $violations = $this->validator->validate($data, $constraints);
        if (count($violations) > 0) {
            return $this->json(['errors' => (string)$violations], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $question = $data['question'];
        $now = new \DateTimeImmutable();

        $consultation = new AIConsultation();
        $consultation->setUser($user);
        $consultation->setQuestion($question);
        $consultation->setModel('mock-llm');
        $consultation->setRawPrompt(json_encode(['question' => $question]));
        $consultation->setAiResponse(['answer' => 'This is a mocked response', 'sources' => []]);

        $this->entityManager->persist($consultation);
        $this->entityManager->flush();

        return $this->json([
            'id' => $consultation->getId(),
            'question' => $question,
            'model' => 'mock-llm',
            'answer' => 'This is a mocked response',
            'sources' => [],
            'createdAt' => $now->format(\DateTimeInterface::ATOM),
        ], Response::HTTP_CREATED);
    }

    #[Route('/vision', name: 'api_ai_vision', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function vision(Request $request, MediaAssetRepository $mediaAssetRepository, DermConditionRepository $dermConditionRepository): JsonResponse
    {
        $data = $request->toArray();

        $constraints = new Assert\Collection([
            'fields' => [
                'imageIds' => [new Assert\NotBlank(), new Assert\Type('array'), new Assert\All([new Assert\Uuid()])],
                'question' => [new Assert\NotBlank(), new Assert\Type('string')],
            ],
            'allowExtraFields' => true,
        ]);

        $violations = $this->validator->validate($data, $constraints);
        if (count($violations) > 0) {
            return $this->json(['errors' => (string)$violations], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $imageIds = $data['imageIds'];
        $question = $data['question'];

        // Găsește asset-urile pentru a confirma că există
        $assets = $mediaAssetRepository->findBy(['id' => $imageIds]);
        if (count($assets) !== count($imageIds)) {
            return $this->json(['error' => 'One or more image IDs are invalid.'], Response::HTTP_NOT_FOUND);
        }

        // Simulează un diagnostic diferențial
        $mockDifferential = [
            ['diagnosis' => 'Acne vulgaris', 'confidence' => 0.42, 'notes' => 'Consider clinical correlation.'],
            ['diagnosis' => 'Rosacea', 'confidence' => 0.28, 'notes' => 'Papulopustular type shares features.'],
            ['diagnosis' => 'Perioral dermatitis', 'confidence' => 0.15, 'notes' => 'Less likely if widespread.']
        ];

        // Caută afecțiuni înrudite pe baza tag-urilor (metodă simplistă)
        $tagsToSearch = [];
        foreach ($mockDifferential as $dx) {
            $tagsToSearch[] = strtolower(str_replace(' ', '-', $dx['diagnosis']));
        }
        $tagsToSearch = array_unique($tagsToSearch);

        $relatedConditions = [];
        if (!empty($tagsToSearch)) {
            // Presupune că repository-ul are o metodă findByTags
            $foundConditions = $dermConditionRepository->findByTags($tagsToSearch);
            foreach ($foundConditions as $condition) {
                $relatedConditions[] = [
                    'slug' => $condition->getSlug(),
                    'title' => $condition->getTitle()
                ];
            }
        }

        $consultation = new AIConsultation();
        $consultation->setUser($user);
        $consultation->setQuestion($question);
        $consultation->setModel('mock-vision-v1');
        $consultation->setInputsMeta(['imageIds' => $imageIds]);
        $consultation->setRawPrompt(json_encode(['question' => $question, 'image_count' => count($imageIds)]));
        $consultation->setAiResponse([
            'differential' => $mockDifferential,
            'relatedConditions' => $relatedConditions
        ]);

        $this->entityManager->persist($consultation);
        $this->entityManager->flush();

        return $this->json([
            'id' => $consultation->getId(),
            'differential' => $mockDifferential,
            'relatedConditions' => $relatedConditions,
            'disclaimer' => 'AI Support: for informational purposes — not a medical diagnosis.'
        ], Response::HTTP_CREATED);
    }
}
