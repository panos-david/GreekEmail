<?php
namespace GreekEmail;

class GreekInflector
{
    protected static array $exceptions = [
        'αρτεμις' => [
            'nominative' => 'Άρτεμις',
            'genitive'   => 'Αρτέμιδος',
            'accusative' => 'Άρτεμι',
            'vocative'   => 'Άρτεμι',
        ],
        'καλλος' => [
            'nominative' => 'Κάλλος',
            'genitive'   => 'Κάλλους',
            'accusative' => 'Κάλλος',
            'vocative'   => 'Κάλλος',
        ],
    ];

    protected static array $nameRules = [
        'm' => [
            'ος'  => ['genitive' => 'ου',  'accusative' => 'ο',   'vocative' => 'o'],
            'ης'  => ['genitive' => 'η',   'accusative' => 'η',   'vocative' => 'η'],
            'ας'  => ['genitive' => 'α',   'accusative' => 'α',   'vocative' => 'α'],
            'ους' => ['genitive' => 'ου',  'accusative' => 'ου',  'vocative'=> 'ου'],
            'ευς' => ['genitive' => 'ευ',  'accusative' => 'ευ',  'vocative'=> 'ευ'],
        ],
        'f' => [
            'α'  => ['genitive' => 'ας',   'accusative' => 'α',   'vocative' => 'α'],
            'η'  => ['genitive' => 'ης',   'accusative' => 'η',   'vocative' => 'η'],
            'ος' => ['genitive' => 'ου',   'accusative' => 'ο',   'vocative' => 'ο'],
            'ις' => ['genitive' => 'ιδος', 'accusative' => 'ι',   'vocative' => 'ιδα'],
            'ου' => ['genitive' => 'ου',   'accusative' => 'ου',  'vocative' => 'ου'],
        ],
        'n' => [
            'ο'  => ['genitive' => 'ου',   'accusative' => 'ο',   'vocative' => 'ο'],
            'ι'  => ['genitive' => 'ιου',  'accusative' => 'ι',   'vocative' => 'ι'],
            'μα' => ['genitive' => 'ματος','accusative' => 'μα',  'vocative' => 'μα'],
            'ος' => ['genitive' => 'ους',  'accusative' => 'ος',  'vocative' => 'ος'],
        ],
    ];

    public static function declineName(string $name, string $gender, string $case = 'nominative'): string
    {
        if ($case === 'nominative') {
            return $name;
        }

        $key = self::stripAccents(mb_strtolower($name));
        if (isset(self::$exceptions[$key][$case])) {
            return self::$exceptions[$key][$case];
        }

        $gender = strtolower($gender);

        if ($case === 'vocative' && $gender === 'm' && self::endsWith($name, 'ος')) {
            $syllables = self::countSyllables($name);
            $ending    = $syllables > 2 ? 'ε' : 'ο';
            return mb_substr($name, 0, -2) . $ending;
        }

        $rules = self::$nameRules[$gender] ?? [];
        foreach ($rules as $ending => $cases) {
            if (self::endsWith($name, $ending)) {
                $base      = mb_substr($name, 0, -mb_strlen($ending));
                $newEnding = $cases[$case] ?? $ending;
                return $base . $newEnding;
            }
        }

        return $name;
    }

    protected static function endsWith(string $haystack, string $needle): bool {
        return mb_substr($haystack, -mb_strlen($needle)) === $needle;
    }

    protected static function countSyllables(string $word): int {
        preg_match_all('/[αεηιουωάέήίόύώ]/iu', $word, $m);
        return max(1, count($m[0]));
    }

    protected static function stripAccents(string $str): string {
        $norm = ['ά'=>'α','έ'=>'ε','ή'=>'η','ί'=>'ι','ό'=>'ο','ύ'=>'υ','ώ'=>'ω',
                 'Ά'=>'α','Έ'=>'ε','Ή'=>'η','Ί'=>'ι','Ό'=>'ο','Ύ'=>'υ','Ώ'=>'ω',
                 'ϊ'=>'ι','ΐ'=>'ι','Ϊ'=>'ι','ϋ'=>'υ','ΰ'=>'υ','Ϋ'=>'υ'];
        return strtr($str, $norm);
    }
}


class EmailTemplateBuilder
{
    protected string $template;
    public function __construct(string $template) {
        $this->template = $template;
    }
    public function render(array $data): string {
        $map = ['nom'=>'nominative','gen'=>'genitive','acc'=>'accusative','voc'=>'vocative'];
        $placeholders = [];
        foreach (['first_name','last_name'] as $field) {
            foreach ($map as $abbr=>$case) {
                $key = '{{'.$field.'_'.$abbr.'}}';
                $placeholders[$key] = GreekInflector::declineName($data[$field] ?? '', $data['gender'] ?? 'm', $case);
            }
        }
        $placeholders['{{email}}'] = $data['email'] ?? '';
        return strtr($this->template, $placeholders);
    }
}
?>
