<?php

/**
 * Class for GUIDs
 *
 * @noinspection  PhpUnused
 * @version       1.2
 * @author        Thomas Kirsch <t.kirsch@webcito.de>
 * @link          https://github.com/ThomasDev-de/php-guid/blob/main/dist/GUID.php
 * @copyright (c) 2023, Thomas Kirsch
 */
class GUID
{
    /**
     * Pattern for valid GUID
     *
     * @access protected
     * @var string
     */
    protected static string $pattern = "/^[a-fA-F\d]{8}(-[a-fA-F\d]{4}){4}[a-fA-F\d]{8}?$/";
    /**
     * Pattern for default protected GUID
     *
     * @access protected
     * @var string
     */
    protected static string $patternDefault = "/^[0]{8}(-[0]{4}){4}[0]{8}?$/";
    /**
     * An default protected GUID
     *
     * @access protected
     * @var string
     */
    protected static string $default = "00000000-0000-0000-0000-000000000000";

    /**
     * Checks whether the GUID is valid
     *
     * @access       public
     *
     * @param string $guid the guid as string
     * @return bool
     */
    public static function isValid(string $guid): bool
    {
        return !empty(preg_match(self::$pattern, trim($guid)));
    }

    /**
     * Checks whether the GUID is protected and contains only zeros
     *
     * @access       public
     *
     * @param string|null $guid
     *
     * @return bool
     */
    public static function isDefault(?string $guid = null): bool
    {
        return (null !== $guid) && !empty(preg_match(self::$patternDefault, trim($guid)));
    }

    /**
     * Get a default protected GUID
     *
     * @access       public
     * @return string
     */
    public static function getDefault(): string
    {
        return self::$default;
    }

    /**
     * Generate a new GUID
     *
     * @access public
     *
     * @param int  $count
     * @param bool $lowerCase
     *
     * @return string|string[]
     */
    public static function generate(int $count = 1, bool $lowerCase = true): string|array
    {
        if (function_exists('com_create_guid') === true) { // only windows
            $guid = trim(com_create_guid(), '{}'); // generate and clean from spaces
        } else {
            try {
                $guid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', random_int(0, 65535), random_int(0, 65535), random_int(0, 65535), random_int(16384, 20479), random_int(32768, 49151), random_int(0, 65535), random_int(0, 65535), random_int(0, 65535));
            } catch (Exception) {
                $guid = self::getDefault();
            }
        }

        $guid = $lowerCase ? strtolower($guid) : strtoupper($guid);

        if ($count === 1) {
            return $guid;
        }
        $return = [$guid];
        for ($i = 1; $i < $count; $i++) {
            $return[] = self::generate(lowerCase: $lowerCase);
        }
        return $return;
    }

    /**
     * Checks for equality
     *
     * @access       public
     *
     * @param string $sourceGuid
     * @param string $targetGuid
     *
     * @return bool
     */
    public static function equals(string $sourceGuid, string $targetGuid): bool
    {
        return $sourceGuid === $targetGuid;
    }

    /**
     * @access  public
     * @return string
     * @throws Exception
     * @example echo new \GUID();
     */
    public function __toString(): string
    {
        return self::generate();
    }
}
