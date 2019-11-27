<?php
declare (strict_types=1);

namespace vansari\csv\encoding;

use ReflectionClass;
use ReflectionException;

/**
 * Class CharsetEncodings - Map of all supported Encoding from php
 * @package vansari\csv\encoding
 * @link https://www.php.net/manual/en/mbstring.supported-encodings.php
 */
class CharsetEncodings
{
    public const UCS_4 = 'UCS-4';
    public const UCS_4BE = 'UCS-4BE';
    public const UCS_4LE = 'UCS-4LE';
    public const UCS_2 = 'UCS-2';
    public const UCS_2BE = 'UCS-2BE';
    public const UCS_2LE = 'UCS-2LE';
    public const UTF_32 = 'UTF-32';
    public const UTF_32BE = 'UTF-32BE';
    public const UTF_32LE = 'UTF-32LE';
    public const UTF_16 = 'UTF-16';
    public const UTF_16BE = 'UTF-16BE';
    public const UTF_16LE = 'UTF-16LE';
    public const UTF_7 = 'UTF-7';
    public const UTF7_IMAP = 'UTF7-IMAP';
    public const UTF_8 = 'UTF-8';
    public const ASCII = 'ASCII';
    public const EUC_JP = 'EUC-JP';
    public const SJIS = 'SJIS';
    public const EUCJP_WIN = 'eucJP-win';
    public const SJIS_WIN = 'SJIS-win';
    public const ISO_2022_JP = 'ISO-2022-JP';
    public const ISO_2022_JP_MS = 'ISO-2022-JP-MS';
    public const CP932 = 'CP932';
    public const CP51932 = 'CP51932';
    public const SJIS_MAC = 'SJIS-mac';
    public const JIS = 'JIS';
    public const JIS_MS = 'JIS-ms';
    public const CP50220 = 'CP50220';
    public const CP50220RAW = 'CP50220raw';
    public const CP50221 = 'CP50221';
    public const CP50222 = 'CP50222';
    public const ISO_8859_1 = 'ISO-8859-1';
    public const ISO_8859_2 = 'ISO-8859-2';
    public const ISO_8859_3 = 'ISO-8859-3';
    public const ISO_8859_4 = 'ISO-8859-4';
    public const ISO_8859_5 = 'ISO-8859-5';
    public const ISO_8859_6 = 'ISO-8859-6';
    public const ISO_8859_7 = 'ISO-8859-7';
    public const ISO_8859_8 = 'ISO-8859-8';
    public const ISO_8859_9 = 'ISO-8859-9';
    public const ISO_8859_10 = 'ISO-8859-10';
    public const ISO_8859_13 = 'ISO-8859-13';
    public const ISO_8859_14 = 'ISO-8859-14';
    public const ISO_8859_15 = 'ISO-8859-15';
    public const ISO_8859_16 = 'ISO-8859-16';
    public const BYTE2BE = 'byte2be';
    public const BYTE2LE = 'byte2le';
    public const BYTE4BE = 'byte4be';
    public const BYTE4LE = 'byte4le';
    public const BASE64 = 'BASE64';
    public const HTML_ENTITIES = 'HTML-ENTITIES';
    public const HTML = 'HTML';
    public const SEVENBIT = '7bit';
    public const EIGHTBIT = '8bit';
    public const EUC_CN = 'EUC-CN';
    public const CP936 = 'CP936';
    public const GB18030 = 'GB18030';
    public const HZ = 'HZ';
    public const EUC_TW = 'EUC-TW';
    public const CP950 = 'CP950';
    public const BIG_5 = 'BIG-5';
    public const EUC_KR = 'EUC-KR';
    public const UHC = 'UHC';
    public const CP949 = 'CP949';
    public const ISO_2022_KR = 'ISO-2022-KR';
    public const WINDOWS_1251 = 'Windows-1251';
    public const CP1251 = 'CP1251';
    public const WINDOWS_1252 = 'Windows-1252';
    public const CP1252 = 'CP1252';
    public const CP866 = 'CP866';
    public const IBM866 = 'IBM866';
    public const KOI8_R = 'KOI8-R';
    public const KOI8_U = 'KOI8-U';
    public const ARMSCII_8 = 'ArmSCII-8';
    public const ARMSCII8 = 'ArmSCII8';

    /**
     * Returns all Constants from this class or an empty array if reflection exception is thrown
     * @return array
     */
    public static function getConstants(): array
    {
        try {
            $class = new ReflectionClass(CharsetEncodings::class);
            return $class->getConstants();
        } catch (ReflectionException $exception) {
            return [];
        }
    }
}