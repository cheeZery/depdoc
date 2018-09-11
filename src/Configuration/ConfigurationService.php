<?php
declare(strict_types=1);

namespace DepDoc\Configuration;

use DepDoc\Configuration\Exception\FailedToParseConfigurationFileException;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class ConfigurationService
{
    /** @var Serializer */
    protected $serializer;
    /** @var ConfigurationFileDefinition[] */
    protected $supportedConfigurationFiles = [];

    /**
     * @param ConfigurationFileDefinition[] $additionalConfigurationFiles
     * @param AbstractNormalizer[] $normalizers
     * @param DecoderInterface[]|EncoderInterface[] $encodersAndDecoders
     */
    public function __construct(
        array $additionalConfigurationFiles = [],
        array $normalizers = [],
        array $encodersAndDecoders = []
    ) {
        $this->supportedConfigurationFiles = array_merge([
            new ConfigurationFileDefinition('.depdoc.json', 'json'),
            new ConfigurationFileDefinition('.depdoc.yaml', 'yaml'),
            new ConfigurationFileDefinition('.depdoc.yml', 'yaml'),
        ], $additionalConfigurationFiles);

        $normalizers = array_merge([new GetSetMethodNormalizer()], $normalizers);
        $encodersAndDecoders = array_merge([new JsonDecode(true), new YamlEncoder()], $encodersAndDecoders);
        $this->serializer = new Serializer($normalizers, $encodersAndDecoders);
    }


    /**
     * @param string $targetDirectory
     * @return ApplicationConfiguration|null
     */
    public function loadFromDirectory(string $targetDirectory): ?ApplicationConfiguration
    {
        foreach ($this->supportedConfigurationFiles as $supportedConfigurationFile) {
            $filepath = $targetDirectory . '/' . $supportedConfigurationFile->getFilename();
            if (file_exists($filepath) === false) {
                continue;
            }

            return $this->loadDefinition($supportedConfigurationFile, $filepath);
        }

        return null;
    }

    /**
     * @param ConfigurationFileDefinition $supportedConfigurationFile
     * @param string $filepath
     * @return ApplicationConfiguration
     */
    protected function loadDefinition(
        ConfigurationFileDefinition $supportedConfigurationFile,
        string $filepath
    ): ApplicationConfiguration {

        try {
            $content = file_get_contents($filepath);
            $dataArray = $this->serializer->decode($content, $supportedConfigurationFile->getFormat());
        } catch (\Throwable $exception) {
            throw new FailedToParseConfigurationFileException($filepath, $exception->getMessage());
        }

        // @TODO: Catch TypeError and expose as invalid configuration file format?
        $configuration = $this->serializer->denormalize($dataArray, ApplicationConfiguration::class);

        return $configuration;
    }
}
