<?php declare(strict_types = 1);
namespace noxkiwi\translator\Traits;

use noxkiwi\core\ErrorHandler;
use noxkiwi\singleton\Exception\SingletonException;
use noxkiwi\translator\Translator;
use function strtoupper;

/**
 * I am the TranslationTrait.
 *
 * @package      noxkiwi\translator\Traits
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 - 2021 noxkiwi
 * @version      1.0.1
 * @link         https://nox.kiwi/
 */
trait TranslatorTrait
{
    /**
     * I will translate the given key.
     * I will suppress any Exception that may occur.
     *
     * @param string     $key
     * @param array|null $context
     *
     * @return       string
     */
    public function translate(string $key, array $context = null): string
    {
        return static::sTranslate($key, $context);
    }

    /**
     * I will translate the given key.
     * I will suppress any Exception that may occur.
     *
     * @param string     $key
     * @param array|null $context
     *
     * @return       string
     */
    public static function sTranslate(string $key, array $context = null): string
    {
        $key     = strtoupper($key);
        $context ??= [];
        try {
            return Translator::getInstance()->translate($key, $context);
        } catch (SingletonException $exception) {
            ErrorHandler::handleException($exception);

            return $key;
        }
    }
}
