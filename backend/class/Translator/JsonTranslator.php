<?php declare(strict_types = 1);
namespace noxkiwi\translator\Translator;

use noxkiwi\core\ErrorHandler;
use noxkiwi\core\Exception\InvalidJsonException;
use noxkiwi\core\Filesystem;
use noxkiwi\core\Helper\JsonHelper;
use noxkiwi\hook\Hook;
use noxkiwi\singleton\Exception\SingletonException;
use noxkiwi\translator\Path;
use noxkiwi\translator\Translator;
use function str_replace;

/**
 * I am the Translator that uses JSON files.
 *
 * @package      noxkiwi\translator\Translator
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class JsonTranslator extends Translator
{
    /**
     * @inheritDoc
     */
    public function getKeys(): array
    {
        $ret = [];
        foreach ($this->getTranslations() as $key => $translation) {
            $ret[] = $key;
        }

        return $ret;
    }

    /**
     * I will return the complete translation file content
     *
     * @return array
     */
    private function getTranslations(): array
    {
        $translationFile = Path::TRANSLATION_DIR . self::getLanguage() . '.json';
        $translationPath = Path::getInheritedPath($translationFile);
        try {
            if (! Filesystem::getInstance()->fileAvailable($translationPath)) {
                Hook::getInstance()->fire('TRANSLATE_TRANSLATION_FILE_MISSING', $translationPath);

                return [];
            }

            return JsonHelper::decodeFileToArray($translationPath);
        } catch (SingletonException | InvalidJsonException $exception) {
            ErrorHandler::handleException($exception);

            return [];
        }
    }

    /**
     * @inheritDoc
     */
    public function getLanguages(): array
    {
        $languageFiles = $this->getLanguageFiles();
        $ret           = [];
        foreach ($languageFiles as $languageFile) {
            $languageFile = str_replace('.json', '', $languageFile);
            $languageFile = Translator::getLanguageCode($languageFile);
            if ($languageFile === '') {
                continue;
            }
            $ret[] = $languageFile;
        }

        return $ret;
    }

    /**
     * I will solely return the list of language files in the translation dir.
     * @see \noxkiwi\translator\Path::TRANSLATION_DIR
     * @return array
     */
    private function getLanguageFiles(): array
    {
        try {
            return Filesystem::getInstance()->dirList(Path::getHomeDir() . Path::TRANSLATION_DIR);
        } catch (SingletonException) {
            return [];
        }
    }

    /**
     * @inheritDoc
     */
    protected function getTranslation(string $key): string
    {
        $keys = $this->getTranslations();
        if (! isset($keys[$key])) {
            return $key;
        }

        return $keys[$key];
    }
}
