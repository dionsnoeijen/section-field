<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Api\Controller;

use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tardigrades\SectionField\SectionFieldInterface\CreateSection;
use Tardigrades\SectionField\SectionFieldInterface\Form;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\ReadOptions;

class RestController
{
    /** @var ReadSection */
    private $readSection;

    /** @var CreateSection */
    private $createSection;

    /** @var Form */
    private $form;

    public function __construct(
        CreateSection $createSection,
        ReadSection $readSection,
        Form $form
    ) {
        $this->readSection = $readSection;
        $this->createSection = $createSection;
        $this->form = $form;
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
     * @return Response
     */
    public function createEntry(string $sectionHandle): Response
    {
        $form = $this->form->buildFormForSection(
            $sectionHandle
        );
        $form->handleRequest();

        if ($form->isValid()) {
            $data = $form->getData();
        }

        header('Content-Type: application/json');
        return new Response(['created' => 'valid']);
    }

    /**
     * PUT (Update) an entry by it's id
     * @param int $id
     */
    public function updateEntryById(int $id)
    {

    }

    /**
     * PUT (Update) an entry by one of it's field values
     * Use this with a slug
     *
     * @param string $fieldHandle
     * @param string $value
     */
    public function updateEntryBy(string $fieldHandle, string $value)
    {

    }

    /**
     * DELETE an entry by it's id
     * @param int $id
     */
    public function deleteEntryById(int $id)
    {

    }

    /**
     * DELETE an entry by it's slug
     * @param string $fieldHandle
     * @param string $value
     */
    public function deleteEntryBy(string $fieldHandle, string $value)
    {

    }
}
