<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Api\Controller;

use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\SectionField\SectionFieldInterface\CreateSection;
use Tardigrades\SectionField\SectionFieldInterface\Form;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\ReadOptions;
use Tardigrades\SectionField\ValueObject\SectionFormOptions;

class RestController
{
    /** @var ReadSection */
    private $readSection;

    /** @var CreateSection */
    private $createSection;

    /** @var Form */
    private $form;

    /** @var SectionManager */
    private $sectionManager;

    /** @var RequestStack */
    private $requestStack;

    /**
     * RestController constructor.
     * @param CreateSection $createSection
     * @param ReadSection $readSection
     * @param Form $form
     * @param SectionManager $sectionManager
     * @param RequestStack $requestStack
     */
    public function __construct(
        CreateSection $createSection,
        ReadSection $readSection,
        Form $form,
        SectionManager $sectionManager,
        RequestStack $requestStack
    ) {
        $this->readSection = $readSection;
        $this->createSection = $createSection;
        $this->form = $form;
        $this->sectionManager = $sectionManager;
        $this->requestStack = $requestStack;
    }

    /**
     * OPTIONS (get) information about the section so you can build
     * awesome forms in your spa, or whatever you need it for.
     * @param string $sectionHandle
     * @return JsonResponse
     */
    public function getSectionInfo(string $sectionHandle): JsonResponse
    {
        $response = [];

        $section = $this->sectionManager->readByHandle($sectionHandle);

        $response['name'] = (string) $section->getName();
        $response['handle'] = (string) $section->getHandle();

        /** @var Field $field */
        foreach ($section->getFields() as $field) {
            $fieldInfo = [
                (string) $field->getHandle() => $field->getConfig()->toArray()['field']
            ];
            $response['fields'][] = $fieldInfo;
        }

        header('Content-Type: application/json');
        return new JsonResponse($response);
    }

    /**
     * GET an entry by id
     * @param string $sectionHandle
     * @param string $id
     * @return Response
     */
    public function getEntryById(string $sectionHandle, string $id): Response
    {
        $readOptions = ReadOptions::fromArray([
            ReadOptions::SECTION => $sectionHandle,
            ReadOptions::ID => (int) $id
        ]);

        $entry = $this->readSection->read($readOptions)[0];

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($entry, 'json');

        header('Content-Type: application/json');
        return new Response($jsonContent);
    }

    /**
     * GET an entry by it's slug
     * @param string $sectionHandle
     * @param string $slug
     * @return Response
     */
    public function getEntryBySlug(string $sectionHandle, string $slug): Response
    {
        $readOptions = ReadOptions::fromArray([
            'section' => $sectionHandle,
            'slug' => $slug
        ]);

        $entry = $this->readSection->read($readOptions)[0];

        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($entry, 'json');

        header('Content-Type: application/json');
        return new Response($jsonContent);
    }

    /**
     * GET Multiple entries
     * @todo: I might want to make the offset, limit, orderby ans sort GET parameters.
     * @param string $sectionHandle
     * @param string $offset
     * @param string $limit
     * @param string $orderBy
     * @param string $sort
     * @return Response
     */
    public function getEntries(
        string $sectionHandle,
        string $offset,
        string $limit,
        string $orderBy,
        string $sort
    ) {
        $readOptions = [
            ReadOptions::SECTION => $sectionHandle,
            ReadOptions::OFFSET => $offset,
            ReadOptions::LIMIT => $limit
        ];

        if (!empty($orderBy) && !empty($sort)) {
            $readOptions[ReadOptions::ORDER_BY] = [$orderBy => $sort];
        }

        $entries = $this->readSection->read(ReadOptions::fromArray($readOptions));
        $serializer = SerializerBuilder::create()->build();

        $result = [];
        foreach ($entries as $entry) {
            $result[] = $serializer->serialize($entry, 'json');
        }

        header('Content-Type: application/json');
        return new Response('[' . implode(',', $result) . ']');
    }

    /**
     * POST a new entry
     * @param string $sectionHandle
     * @return JsonResponse
     */
    public function createEntry(string $sectionHandle): JsonResponse
    {
        $response = [];
        $form = $this->form->buildFormForSection(
            $sectionHandle,
            null,
            false
        );
        $form->handleRequest();
        $responseCode = 200;
        if ($form->isValid()) {
            $response = $this->save($form);
        } else {
            $responseCode = 400;
            $response['errors'] = $this->getFormErrors($form);
        }

        return new JsonResponse($response, $responseCode);
    }

    /**
     * PUT (Update) an entry by it's id
     *
     * @param string $sectionHandle
     * @param int $id
     * @return JsonResponse
     */
    public function updateEntryById(string $sectionHandle, int $id): JsonResponse
    {
        $response = [];

        $form = $this->form->buildFormForSection(
            $sectionHandle,
            SectionFormOptions::fromArray([
                'id' => $id
            ]),
            false
        );
        $form->handleRequest();
        $responseCode = 200;
        if ($form->isValid()) {
            $response = $this->save($form);
        } else {
            $responseCode = 400;
            $response['errors'] = $this->getFormErrors($form);
        }

        return new JsonResponse($response, $responseCode);
    }

    /**
     * PUT (Update) an entry by one of it's field values
     * Use this with a slug
     *
     * @param string $sectionHandle
     * @param string $slug
     * @return JsonResponse
     */
    public function updateEntryBySlug(string $sectionHandle, string $slug): JsonResponse
    {
        $response = [];

        $form = $this->form->buildFormForSection(
            $sectionHandle,
            SectionFormOptions::fromArray([
                'slug' => $slug
            ]),
            false
        );
        $form->handleRequest();
        $responseCode = 200;
        if ($form->isValid()) {
            $response = $this->save($form);
        } else {
            $responseCode = 400;
            $response['errors'] = $this->getFormErrors($form);
        }

        return new JsonResponse($response, $responseCode);
    }

    /**
     * DELETE an entry by it's id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteEntryById(int $id): JsonResponse
    {
        
    }

    /**
     * DELETE an entry by it's slug
     * @param string $sectionHandle
     * @param string $slug
     * @return JsonResponse
     */
    public function deleteEntryBySlug(string $sectionHandle, string $slug): JsonResponse
    {

    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function save(FormInterface $form): array
    {
        $response = [];
        $data = $form->getData();
        $request = $this->requestStack->getCurrentRequest();
        $relationships = $this->form->hasRelationship($request->get('form'));
        try {
            $this->createSection->save($data, $relationships);
            $response['success'] = true;
            $response['errors'] = false;
        } catch (\Exception $exception) {
            $responseCode = 500;
            $response['exception'] = $exception->getMessage();
        }

        return $response;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $field=>$formError) {
            $errors[] = $formError->getMessage();
        }

        /** @var FormInterface $child */
        foreach ($form as $child) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }
}
