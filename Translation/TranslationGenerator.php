<?php
namespace Scriber\Bundle\JsTranslationBundle\Translation;

use Scriber\Bundle\JsTranslationBundle\Exception\InvalidPageException;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;

class TranslationGenerator
{
    /**
     * @var TranslatorBagInterface
     */
    private $translatorBag;

    /**
     * @var array
     */
    private $config;

    /**
     * @param TranslatorBagInterface $translatorBag
     * @param array $config
     */
    public function __construct(TranslatorBagInterface $translatorBag, array $config)
    {
        $this->translatorBag = $translatorBag;
        $this->config = array_merge(['pages' => []], $config);
    }

    /**
     * @return array
     */
    public function getPages(): array
    {
        return array_keys($this->config['pages']);
    }

    /**
     * @param string $page
     * @param string $locale
     *
     * @return array
     */
    public function getTranslations(string $page, ?string $locale = null): array
    {
        $config = $this->getPageConfig($page);

        $messages = [];
        $catalogue = $this->translatorBag->getCatalogue($locale);

        foreach ($config as $domain => $keys) {
            foreach ($this->getMessages($catalogue, $domain, $keys) as $domainLocale => $messageStrings) {
                if (!$messageStrings) {
                    continue;
                }

                if (!array_key_exists($domainLocale, $messages)) {
                    $messages[$domainLocale] = [];
                }

                $messages[$domainLocale][$domain] = $messageStrings;
            }
        }

        return $messages;

    }

    /**
     * @param string $page
     *
     * @return array
     */
    public function getPageConfig(string $page)
    {
        if (!array_key_exists($page, $this->config['pages'])) {
            throw new InvalidPageException(
                sprintf(
                    'Page "%s" not found in current configuration. Available pages are: "%s"',
                    $page,
                    implode('", "', array_keys($this->config['pages']))
                )
            );
        }

        return (array) $this->config['pages'][$page];
    }

    /**
     * @param MessageCatalogueInterface $catalogue
     * @param string $domain
     * @param array $keys
     *
     * @return array
     */
    private function getMessages(MessageCatalogueInterface $catalogue, string $domain, array $keys)
    {
        $current = $catalogue;
        $previous = [];
        $messages = [];

        while ($current !== null) {
            $all = $current->all($domain);

            if (count($keys) === 0 || in_array('*', $keys)) {
                $currentMessages = array_diff_key($all, $previous);
            } else {
                $currentMessages = [];

                foreach ($keys as $key) {
                    if (substr($key, -1) === '*') {
                        $currentMessages += $this->findWildcardMessages($key, $all, $previous);
                    } else if (!array_key_exists($key, $previous) && array_key_exists($key, $all)) {
                        $currentMessages[$key] = $all[$key];
                    }
                }
            }

            $messages[$current->getLocale()] = $previous = $currentMessages;
            $current = $current->getFallbackCatalogue();
        }

        return $messages;
    }

    /**
     * @param string $key
     * @param array $all
     * @param array $previous
     *
     * @return array
     */
    private function findWildcardMessages(string $key, array $all, array $previous)
    {
        $key = (string) substr($key, 0, -1);

        return array_filter(
            $all,
            function ($v) use ($key, $previous) {
                return !array_key_exists($v, $previous) && strpos($v, $key) === 0;
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
