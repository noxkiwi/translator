<?php declare(strict_types = 1);
namespace noxkiwi\translator;

use Exception;
use noxkiwi\cache\Cache;
use noxkiwi\core\Cookie;
use noxkiwi\core\ErrorHandler;
use noxkiwi\core\Helper\StringHelper;
use noxkiwi\core\Request;
use noxkiwi\core\Session;
use noxkiwi\singleton\Exception\SingletonException;
use noxkiwi\singleton\Singleton;
use noxkiwi\translator\Interfaces\TranslatorInterface;
use function explode;
use function in_array;
use function is_string;
use function strtoupper;
use const E_USER_NOTICE;

/**
 * I am the main Translation class.
 *
 * @package      noxkiwi\translator
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2020 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class Translator extends Singleton implements TranslatorInterface
{
    protected const USE_DRIVER     = true;
    public const    LANGUAGE_KEY   = 'lang';
    public const    LANGUAGE_DE_DE = 'de-DE';
    public const    LANGUAGE_EN_US = 'en-US';
    public const    LANGUAGE_EN_NZ = 'en-NZ';
    /** @var string I set the language the Request is performed in. */
    public static string $language;
    /** @var string I set the default language for the App. */
    public static string $defaultLanguage = self::LANGUAGE_EN_US;
    /** @var array I am the central collection of all languages that are available across the whole framework. */
    public static array $languageCodes = [
        'af-ZA'              => 'Afrikaans - South Africa',
        'sq-AL'              => 'Albanian - Albania',
        'ar-DZ'              => 'Arabic - Algeria',
        'ar-BH'              => 'Arabic - Bahrain',
        'ar-EG'              => 'Arabic - Egypt',
        'ar-IQ'              => 'Arabic - Iraq',
        'ar-JO'              => 'Arabic - Jordan',
        'ar-KW'              => 'Arabic - Kuwait',
        'ar-LB'              => 'Arabic - Lebanon',
        'ar-LY'              => 'Arabic - Libya',
        'ar-MA'              => 'Arabic - Morocco',
        'ar-OM'              => 'Arabic - Oman',
        'ar-QA'              => 'Arabic - Qatar',
        'ar-SA'              => 'Arabic - Saudi Arabia',
        'ar-SY'              => 'Arabic - Syria',
        'ar-TN'              => 'Arabic - Tunisia',
        'ar-AE'              => 'Arabic - United Arab Emirates',
        'ar-YE'              => 'Arabic - Yemen',
        'hy-AM'              => 'Armenian - Armenia',
        'Cy-az-AZ'           => 'Azeri (Cyrillic) - Azerbaijan',
        'Lt-az-AZ'           => 'Azeri (Latin) - Azerbaijan',
        'eu-ES'              => 'Basque - Basque',
        'be-BY'              => 'Belarusian - Belarus',
        'bg-BG'              => 'Bulgarian - Bulgaria',
        'ca-ES'              => 'Catalan - Catalan',
        'zh-CN'              => 'Chinese - China',
        'zh-HK'              => 'Chinese - Hong Kong SAR',
        'zh-MO'              => 'Chinese - Macau SAR',
        'zh-SG'              => 'Chinese - Singapore',
        'zh-TW'              => 'Chinese - Taiwan',
        'zh-CHS'             => 'Chinese (Simplified)',
        'zh-CHT'             => 'Chinese (Traditional)',
        'hr-HR'              => 'Croatian - Croatia',
        'cs-CZ'              => 'Czech - Czech Republic',
        'da-DK'              => 'Danish - Denmark',
        'div-MV'             => 'Dhivehi - Maldives',
        'nl-BE'              => 'Dutch - Belgium',
        'nl-NL'              => 'Dutch - The Netherlands',
        'en-AU'              => 'English - Australia',
        'en-BZ'              => 'English - Belize',
        'en-CA'              => 'English - Canada',
        'en-CB'              => 'English - Caribbean',
        'en-IE'              => 'English - Ireland',
        'en-JM'              => 'English - Jamaica',
        self::LANGUAGE_EN_NZ => 'English - New Zealand',
        'en-PH'              => 'English - Philippines',
        'en-ZA'              => 'English - South Africa',
        'en-TT'              => 'English - Trinidad and Tobago',
        'en-GB'              => 'English - United Kingdom',
        self::LANGUAGE_EN_US => 'English - United States',
        'en-ZW'              => 'English - Zimbabwe',
        'et-EE'              => 'Estonian - Estonia',
        'fo-FO'              => 'Faroese - Faroe Islands',
        'fa-IR'              => 'Farsi - Iran',
        'fi-FI'              => 'Finnish - Finland',
        'fr-BE'              => 'French - Belgium',
        'fr-CA'              => 'French - Canada',
        'fr-FR'              => 'French - France',
        'fr-LU'              => 'French - Luxembourg',
        'fr-MC'              => 'French - Monaco',
        'fr-CH'              => 'French - Switzerland',
        'gl-ES'              => 'Galician - Galician',
        'ka-GE'              => 'Georgian - Georgia',
        'de-AT'              => 'German - Austria',
        self::LANGUAGE_DE_DE => 'German - Germany',
        'de-LI'              => 'German - Liechtenstein',
        'de-LU'              => 'German - Luxembourg',
        'de-CH'              => 'German - Switzerland',
        'el-GR'              => 'Greek - Greece',
        'gu-IN'              => 'Gujarati - India',
        'he-IL'              => 'Hebrew - Israel',
        'hi-IN'              => 'Hindi - India',
        'hu-HU'              => 'Hungarian - Hungary',
        'is-IS'              => 'Icelandic - Iceland',
        'id-ID'              => 'Indonesian - Indonesia',
        'it-IT'              => 'Italian - Italy',
        'it-CH'              => 'Italian - Switzerland',
        'ja-JP'              => 'Japanese - Japan',
        'kn-IN'              => 'Kannada - India',
        'kk-KZ'              => 'Kazakh - Kazakhstan',
        'kok-IN'             => 'Konkani - India',
        'ko-KR'              => 'Korean - Korea',
        'ky-KZ'              => 'Kyrgyz - Kazakhstan',
        'lv-LV'              => 'Latvian - Latvia',
        'lt-LT'              => 'Lithuanian - Lithuania',
        'mk-MK'              => 'Macedonian (FYROM)',
        'ms-BN'              => 'Malay - Brunei',
        'ms-MY'              => 'Malay - Malaysia',
        'mr-IN'              => 'Marathi - India',
        'mn-MN'              => 'Mongolian - Mongolia',
        'nb-NO'              => 'Norwegian (Bokmål) - Norway',
        'nn-NO'              => 'Norwegian (Nynorsk) - Norway',
        'pl-PL'              => 'Polish - Poland',
        'pt-BR'              => 'Portuguese - Brazil',
        'pt-PT'              => 'Portuguese - Portugal',
        'pa-IN'              => 'Punjabi - India',
        'ro-RO'              => 'Romanian - Romania',
        'ru-RU'              => 'Russian - Russia',
        'sa-IN'              => 'Sanskrit - India',
        'Cy-sr-SP'           => 'Serbian (Cyrillic) - Serbia',
        'Lt-sr-SP'           => 'Serbian (Latin) - Serbia',
        'sk-SK'              => 'Slovak - Slovakia',
        'sl-SI'              => 'Slovenian - Slovenia',
        'es-AR'              => 'Spanish - Argentina',
        'es-BO'              => 'Spanish - Bolivia',
        'es-CL'              => 'Spanish - Chile',
        'es-CO'              => 'Spanish - Colombia',
        'es-CR'              => 'Spanish - Costa Rica',
        'es-DO'              => 'Spanish - Dominican Republic',
        'es-EC'              => 'Spanish - Ecuador',
        'es-SV'              => 'Spanish - El Salvador',
        'es-GT'              => 'Spanish - Guatemala',
        'es-HN'              => 'Spanish - Honduras',
        'es-MX'              => 'Spanish - Mexico',
        'es-NI'              => 'Spanish - Nicaragua',
        'es-PA'              => 'Spanish - Panama',
        'es-PY'              => 'Spanish - Paraguay',
        'es-PE'              => 'Spanish - Peru',
        'es-PR'              => 'Spanish - Puerto Rico',
        'es-ES'              => 'Spanish - Spain',
        'es-UY'              => 'Spanish - Uruguay',
        'es-VE'              => 'Spanish - Venezuela',
        'sw-KE'              => 'Swahili - Kenya',
        'sv-FI'              => 'Swedish - Finland',
        'sv-SE'              => 'Swedish - Sweden',
        'syr-SY'             => 'Syriac - Syria',
        'ta-IN'              => 'Tamil - India',
        'tt-RU'              => 'Tatar - Russia',
        'te-IN'              => 'Telugu - India',
        'th-TH'              => 'Thai - Thailand',
        'tr-TR'              => 'Turkish - Turkey',
        'uk-UA'              => 'Ukrainian - Ukraine',
        'ur-PK'              => 'Urdu - Pakistan',
        'Cy-uz-UZ'           => 'Uzbek (Cyrillic) - Uzbekistan',
        'Lt-uz-UZ'           => 'Uzbek (Latin) - Uzbekistan',
        'vi-VN'              => 'Vietnamese - Vietnam'
    ];
    /** @var array I am the list of translations that were used in this request. For performance. */
    private static array $translations = [];

    /**
     * I will solely return a normalized string representation of the given $key.
     *
     * @param string $key
     *
     * @return string
     */
    final public static function normalizeKey(string $key): string
    {
        return strtoupper($key);
    }

    /**
     * I will solely utilize the method translate($key, $context) on the default Translator since this is the
     * most utilized one.
     *
     * Chances are pretty low that you really want to use different Translator configurations at once.
     *
     * @param string     $key
     * @param array|null $context
     *
     * @return string
     */
    final public static function get(string $key, array $context = null): string
    {
        try {
            return static::getInstance()->translate($key, $context);
        } catch (SingletonException $exception) {
            ErrorHandler::handleException($exception, E_USER_NOTICE);

            return $key;
        }
    }

    /**
     * @inheritDoc
     * @return string
     */
    final public function translate(string $key, array $context = null): string
    {
        $context ??= [];
        $cache   = null;
        $key     = self::normalizeKey($key);
        try {
            $cache = Cache::getInstance();
        } catch (Exception) {
        }
        // Check second level cache.
        if (isset(self::$translations[$key])) {
            return StringHelper::interpolate(self::$translations[$key], $context);
        }
        // Check first level cache.
        if ($cache) {
            $cacheGroup = static::getCacheGroup();
            $cacheKey   = strtoupper($key);
            $translated = $cache->get($cacheGroup, $cacheKey);
            if (is_string($translated)) {
                self::$translations[$key] = $translated;

                return StringHelper::interpolate($translated, $context);
            }
        }
        $translated = $this->getTranslation($key);
        if ($cache) {
            $cache->set($cacheGroup, $cacheKey, $translated);
            self::$translations[$key] = $translated;
        }

        return StringHelper::interpolate($translated, $context);
    }

    /**
     * I will return a basic cache group name for this class
     *
     * @return       string
     */
    public static function getCacheGroup(): string
    {
        return Cache::DEFAULT_PREFIX . 'TRANSLATION_' . strtoupper(static::getLanguage());
    }

    /**
     * I will return the language code that is either forced by the user or set by the application's default language.
     * <br />I may also set the app's language for this distinct cookie and session if the language is given through
     * the request interface.
     * The priorities are the following:
     *  - Previously set language stored in static $language
     *  - Language is requested from user-input using the request pattern
     *  - Language is requested from the user's cookie
     *  - Language is requested from the user's session
     *  - Language is obtained from the defaultLanguage of the application.
     *
     * @return       string
     */
    public static function getLanguage(): string
    {
        if (empty(static::$language)) {
            static::$language = self::decideLanguage();
        }

        return static::$language;
    }

    /**
     * I will decide on the languate the user gets to see.
     * @return string
     */
    private static function decideLanguage(): string
    {
        try {
            $language = static::getLanguageCode((string)Request::getInstance()->get(static::LANGUAGE_KEY));
            if ($language !== null) {
                return $language;
            }
            $language = static::getLanguageCode((string)Cookie::getInstance()->get(static::LANGUAGE_KEY));
            if ($language !== null) {
                return $language;
            }
            $language = static::getLanguageCode((string)Session::getInstance()->get(static::LANGUAGE_KEY));
            if ($language !== null) {
                return $language;
            }
            $language = static::getLanguageCode(explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0]);
            if ($language !== null) {
                return $language;
            }
        } catch (Exception) {
            // Entirely suppressed.
        }

        return static::$defaultLanguage;
    }

    /**
     * I will return true if the given language is available from the
     * @see \noxkiwi\translator\Interfaces\TranslatorInterface
     *
     * @param string|null $language
     *
     * @return       string
     */
    public static function getLanguageCode(?string $language = null): string
    {
        $language ??= Request::getInstance()->get(static::LANGUAGE_KEY, static::$defaultLanguage);
        if (! empty(static::$languageCodes[$language])) {
            return $language;
        }

        return static::$defaultLanguage;
    }

    /**
     * I will return the text that is translated and identified by $key.
     *
     * @param string $key
     *
     * @return string
     */
    abstract protected function getTranslation(string $key): string;

    /**
     * I will send the given language to the user's session and cookie driver's interfaces.
     *
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        if (! in_array($language, static::getLanguages(), true)) {
            return;
        }
        static::$language = $language;
        try {
            Cookie::getInstance()->set(static::LANGUAGE_KEY, $language);
            Session::getInstance()->set(static::LANGUAGE_KEY, $language);
        } catch (Exception $exception) {
            ErrorHandler::handleException($exception);
        }
    }
}
