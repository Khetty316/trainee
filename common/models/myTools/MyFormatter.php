<?php

namespace common\models\myTools;

use Yii;
use yii\base\Model;
use yii\helpers\Html;

class MyFormatter extends Model {

    const IN_PROGRESS_TIERS = [
        ['color' => 'lightgray', 'label' => 'On Schedule'],
    ];
    
// Due Soon: yellow/orange family, more urgent = closer to today
    const DUE_SOON_TIERS = [
//        ['min' => 0, 'color' => 'yellow', 'label' => '0-1 day away'],
//        ['min' => 3, 'color' => 'gold', 'label' => '2-4 days away'],
//        ['min' => 5, 'color' => 'orange', 'label' => '5 days away'],
        ['min' => 5, 'color' => 'orange', 'label' => '5 days away'],
        ['min' => 3, 'color' => 'gold', 'label' => '2-4 days away'],
        ['min' => 0, 'color' => 'yellow', 'label' => '0-1 day away'],
    ];
    const OVERDUE_TIERS = [
        ['max' => 0, 'color' => 'orangered', 'label' => '0 days (today)'],
        ['max' => 14, 'color' => 'red', 'label' => '1-14 days'],
        ['max' => 28, 'color' => 'crimson', 'label' => '15-28 days'],
        ['max' => PHP_INT_MAX, 'color' => 'firebrick', 'label' => '29+ days'],
    ];
    const COMPLETION_TIERS = [
        ['max' => 0, 'color' => 'skyblue', 'label' => '0 days late (on time)'],
        ['max' => 14, 'color' => 'deepskyblue', 'label' => '1-14 days late'],
        ['max' => 28, 'color' => 'dodgerblue', 'label' => '15-28 days late'],
        ['max' => PHP_INT_MAX, 'color' => 'royalblue', 'label' => '29+ days late'],
    ];
    const EARLY_TIERS = [
        ['max' => 0, 'color' => 'greenyellow', 'label' => '0 days early (on time)'],
        ['max' => 14, 'color' => 'lightgreen', 'label' => '1-14 days early'],
        ['max' => 28, 'color' => 'limegreen', 'label' => '15-28 days early'],
        ['max' => PHP_INT_MAX, 'color' => 'green', 'label' => '29+ days early'],
    ];
    const LATE_TIERS = [
//        ['max' => 0, 'color' => 'violet', 'label' => '0 days late (on time)'],
        ['max' => 14, 'color' => 'orchid', 'label' => '1-14 days late'],
        ['max' => 28, 'color' => 'darkorchid', 'label' => '15-28 days late'],
        ['max' => PHP_INT_MAX, 'color' => 'indigo', 'label' => '29+ days late'],
    ];

    // Maps the named CSS colors used above to hex, so contrast calculation works.
    // Extend this list if you introduce new named colors in any tier.
    private static $namedColorMap = [
        'red' => '#ff0000',
        'crimson' => '#dc143c',
        'firebrick' => '#b22222',
        'orange' => '#ffa500',
        'gold' => '#ffd700',
        'yellow' => '#ffff00',
        'greenyellow' => '#adff2f',
        'limegreen' => '#32cd32',
        'green' => '#008000',
        'skyblue' => '#87ceeb',
        'deepskyblue' => '#00bfff',
        'dodgerblue' => '#1e90ff',
        'orchid' => '#da70d6',
        'darkorchid' => '#9932cc',
        'indigo' => '#4b0082',
    ];

    /**
     * Get color for "In Progress" status based on completion percentage
     * @param float $percent Average completion percentage (0-100)
     * @return string Color hex code
     */
    public static function getInProgressColor($percent) {
        if ($percent >= 75) {
            return '#cce5ff'; // Nearing Complete (light blue)
        } elseif ($percent >= 50) {
            return '#d4edda'; // In Progress (light green)
        } elseif ($percent >= 25) {
            return '#e8f4f8'; // Early Stage (very light blue)
        } else {
            return '#f8f9fa'; // Not Started (light gray)
        }
    }

    public static function getTierColor(array $tiers, int $days, string $mode = 'max') {
        foreach ($tiers as $tier) {
            if ($mode === 'max' && $days <= $tier['max']) {
                return $tier['color'];
            }
            if ($mode === 'min' && $days >= $tier['min']) {
                return $tier['color'];
            }
        }
        return end($tiers)['color'];
    }

    public static function interpolateColor($startHex, $endHex, $ratio) {
        $ratio = max(0, min(1, $ratio));
        $start = sscanf($startHex, "#%02x%02x%02x");
        $end = sscanf($endHex, "#%02x%02x%02x");
        $r = round($start[0] + ($end[0] - $start[0]) * $ratio);
        $g = round($start[1] + ($end[1] - $start[1]) * $ratio);
        $b = round($start[2] + ($end[2] - $start[2]) * $ratio);
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    /**
     * Picks black or white text based on background luminance.
     * Now handles both named CSS colors (e.g. 'crimson') and hex strings.
     */
    public static function getContrastTextColor($color) {
        $hex = self::$namedColorMap[$color] ?? $color;
        $rgb = sscanf($hex, "#%02x%02x%02x");

        if (!$rgb || count($rgb) < 3 || in_array(null, $rgb, true)) {
            return '#000'; // safe fallback if color couldn't be parsed
        }

        [$r, $g, $b] = $rgb;
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $luminance > 0.6 ? '#000' : '#fff';
    }

    /**
     * Renders a horizontal gradient strip + label for one tiered status,
     * built directly from the tier constants above — so the legend can
     * never fall out of sync with the actual colors used in the grid.
     */
    public static function renderTierLegend($title, array $tiers) {
        $swatches = '';
        $labels = '';

        foreach ($tiers as $tier) {
            $swatches .= '<span style="flex:1;background:' . Html::encode($tier['color']) . ';height:18px;"></span>';
            $labels .= '<span style="flex:1;text-align:center;font-size:0.75rem;">' . Html::encode($tier['label']) . '</span>';
        }

        return '<div style="margin-bottom:10px;">' .
                '<b>' . Html::encode($title) . '</b><br>' .
                '<div style="display:flex;width:100%;">' . $swatches . '</div>' .
                '<div style="display:flex;width:100%;">' . $labels . '</div>' .
                '</div>';
    }

    public static function renderFlatLegend($label, $bg, $clr = '#000', $border = null) {
        $style = "background:{$bg};color:{$clr};padding:3px 8px;border-radius:4px;";
        if ($border) {
            $style .= "border:1px solid {$border};";
        }
        return '<div style="margin-bottom:10px;">' .
                '<span class="badge" style="' . Html::encode($style) . '">' . Html::encode($label) . '</span>' .
                '</div>';
    }

    /**
     * Round the number in 0.5 as unit
     * @param type $num
     * @return string
     */
    public static function floorHalfDecimal($num) {
        if (is_numeric($num)) {
            return floor($num * 2) / 2;
        } else {
            return "0.0";
        }
    }

    /**
     * Round the number down but check if its numeric first
     * @param type $num
     * @return string
     */
    public static function floorWholeNum($num) {
        if (is_numeric($num)) {
            return floor($num);
        } else {
            return "0.0";
        }
    }

    public static function asDecimal0($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 0);
        } else {
            return "";
        }
    }

    public static function asDecimal2($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 2);
        } else {
            return "";
        }
    }

    public static function asDecimal1_emptyDash($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 1);
        } else {
            return "-";
        }
    }

    public static function asDecimal2_emptyDash($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 2);
        } else {
            return "-";
        }
    }

    public static function asDecimal2_emptyZero($num) {
        if (is_numeric($num)) {
            return Yii::$app->formatter->asDecimal($num, 2);
        } else {
            return "0.00";
        }
    }

    public static function asDecimal2NoSeparator($num) {
        if (is_numeric($num)) {
            return str_replace(",", "", Yii::$app->formatter->asDecimal($num, 2));
        } else {
            return "";
        }
    }

    public static function asCurrency($num) {
        if (is_numeric($num)) {
            return "RM " . Yii::$app->formatter->asDecimal($num, 2);
        } else {
            return "#ERROR, NOT NUMBER";
        }
    }

    public static function asDateTime_Read($date) {
        if ($date) {
            $theDate = date_create($date);
            if ($theDate) {
                return date_format($theDate, "d/m/Y H:i:s");
            }
        } else {
            return null;
        }
    }

    public static function getYear($date) {
        $theDate = date_create($date);
        return date_format($theDate, "Y");
    }

    public static function asDateTime_ReaddmYHi($date) {
        if ($date) {
            $theDate = date_create($date);
            if ($theDate) {
                return date_format($theDate, "d/m/Y H:i");
            }
        } else {
            return null;
        }
    }

    public static function fromDateRead_toDateSQL($date) {
        if ($date == "") {
            return null;
        } else {
            $theDate = str_replace('/', '-', $date);
            return date('Y-m-d', strtotime($theDate));
        }
    }

    public static function fromDateTimeSql_toDateSql($date) {
        if ($date == "") {
            return null;
        } else {
            $theDate = date_create($date);
            return date_format($theDate, "Y-m-d");
        }
    }

    public static function asDate_Read($date) {
        if ($date) {
            $theDate = date_create($date);
            return date_format($theDate, 'd/m/Y');
        } else {
            return null;
        }
    }

    public static function asDate_Read_dm($date) {
        $theDate = date_create($date);
        return date_format($theDate, 'd/m');
    }

    public static function asDate_Read_dnY($date) {
        if ($date) {
            $theDate = date_create($date);
            return date_format($theDate, 'd-M-Y');
        } else {
            return " - ";
        }
    }

    public static function asDay_Read($date) {
        $theDate = date_create($date);
        return date_format($theDate, 'D');
    }

    public static function asDayLong_Read($date) {
        $theDate = date_create($date);
        return date_format($theDate, 'l');
    }

    public static function changeDateFormat_readToDB($date) {
        $var = str_replace('/', '-', $date);
        return date('Y-m-d', strtotime($var));
    }

    public static function fromDateTimeExcelMDY_toDateSql($date) {
        if ($date == "") {
            return null;
        } else {
            $ymd = \DateTime::createFromFormat('d/m/Y', $date);
            return $ymd ? $ymd->format('Y-m-d') : false;
        }
    }
}
