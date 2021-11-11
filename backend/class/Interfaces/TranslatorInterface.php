<?php declare(strict_types = 1);
namespace noxkiwi\translator\Interfaces;

/**
 * I am the interface for all Translators.
 *
 * @package      noxkiwi\translator\Interfaces
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
interface TranslatorInterface
{
    /**
     * I will return the translation from the service.
     * <br />$key contains two sections separated by a period (.)
     * <br />The first one  is the translation context.
     * <br />The second one is the distinct translation key inside that context.
     * <br />$context is used to inject dynamic data into the translations.
     *
     * @param string     $key
     * @param array|null $context
     *
     * @return       string
     */
    public function translate(string $key, array $context = null): string;

    /**
     * I will return the languages that are available for translation.
     *
     * @return       array
     */
    public function getLanguages(): array;

    /**
     * I will return the keys that are defined on the translation system for this application.
     *
     * @return       array
     */
    public function getKeys(): array;
}
