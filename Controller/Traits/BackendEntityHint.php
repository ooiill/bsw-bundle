<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use stdClass;

/**
 * @property AbstractController $container
 */
trait BackendEntityHint
{
    /**
     * Relation with keyword
     *
     * @param string $name
     * @param string $keyword
     *
     * @return bool
     */
    public static function relationWithKeyword(string $name, string $keyword)
    {
        return ($name === $keyword) || (strpos($name, "{$keyword}_") === 0) || Helper::strEndWith($name, "_{$keyword}");
    }

    /**
     * Enum is exists
     *
     * @param string $table
     * @param        $item
     * @param array  $args
     *
     * @return bool
     */
    public static function enumIsExists(string $table, $item, array $args): bool
    {
        $enumClass = get_class_vars($args['acme'])['enum'];
        $flag = "{$table}_{$item->name}";
        $prefer = strtoupper(Helper::camelToUnder($flag));
        $secondary = strtoupper(Helper::camelToUnder($item->name));

        if (defined("{$enumClass}::{$prefer}")) {
            return true;
        }
        if (defined("{$enumClass}::{$secondary}")) {
            return true;
        }

        return false;
    }

    /**
     * Entity preview hint
     *
     * @param object   $item
     * @param string   $table
     * @param array    $fields
     * @param array    $args
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityPreviewHint($item, string $table, array $fields, array $args, stdClass $options = null)
    {
        $options = $options ?? new stdClass();

        // type like int
        if (strpos($item->type, 'int') !== false) {
            $options->align = 'center';
        }

        // type eq tinyint
        if ($item->type == Abs::MYSQL_TINYINT && self::enumIsExists($table, $item, $args)) {
            $options->enum = true;
            $options->dress = 'blue';
        }

        // json
        if ($item->type === Abs::MYSQL_JSON) {
            $options->width = 360;
            $options->hook[] = 'BswHook\JsonStringify::class';
            $options->render = 'BswAbs::HTML_PRE';
        }

        // enum map to field
        $map = [];
        if ($enum = $map[$item->name] ?? false) {
            $options->enum = $enum;
        }

        // render need BswAbs::RENDER_CODE
        if (in_array($item->name, ['date', 'key', 'sort'])) {
            $options->align = 'center';
            $options->render = 'BswAbs::RENDER_CODE';
        }

        // render need BswAbs::RENDER_ICON
        if (in_array($item->name, ['icon'])) {
            $options->render = 'BswAbs::RENDER_ICON';
        }

        // render need
        if ($item->length) {
            if ($item->length < 26) {
                $options->align = 'center';
                $options->render = 'BswAbs::RENDER_CODE';
            } elseif ($item->length >= 26 && empty($options->render)) {
                $options->render = 'BswAbs::HTML_TEXT';
            }
        }

        // align need center
        if (
            strpos($item->name, '_time') !== false ||
            strpos($item->name, '_date') !== false
        ) {
            $options->align = 'center';
            $options->render = 'BswAbs::RENDER_CODE';
        }

        // field eq state
        if (in_array($item->name, ['state'])) {
            $options->status = true;
            $options->dress = [0 => 'default', 1 => 'processing'];
            $options->enum = true;
        }

        // hide password
        if (strpos($item->name, 'password') !== false) {
            $options->show = false;
        }

        if ($item->name == 'route_name') {
            $options->enumExtra = true;
        }

        // hook Timestamp
        if (
            (static::relationWithKeyword($item->name, 'time') || static::relationWithKeyword($item->name, 'period')) &&
            strpos($item->type, 'int') !== false
        ) {
            $options->hook[] = 'BswHook\\Timestamp::class';
            $options->width = 190;
        }

        // width
        $widthMap = ['id' => 90, 'add_time' => 190, 'update_time' => 190];
        if ($width = $widthMap[$item->name] ?? null) {
            $options->width = $width;
        }

        if (in_array($item->name, ['rule', 'content', 'remark'])) {
            $options->width = 360;
            $options->render = 'BswAbs::HTML_PRE';
        }

        if ($item->name == Abs::PK) {
            $options->render = 'BswAbs::RENDER_CODE';
        }

        if ($item->name == 'size') {
            $options->width = 150;
            $options->hook[] = 'BswHook\\FileSize::class';
        }

        if (in_array($item->name, ['cdkey', 'google_auth_secret'])) {
            $options->width = 150;
            $options->align = 'center';
            $options->render = 'BswAbs::RENDER_SECRET';
        }

        $needMoneyHook = false;

        foreach (['money', 'amount', 'paid', 'cost', 'price', 'balance'] as $keyword) {
            if (static::relationWithKeyword($item->name, $keyword)) {
                $needMoneyHook = true;
                break;
            }
        }

        foreach (['score', 'percent', 'times', 'count'] as $keyword) {
            if (static::relationWithKeyword($item->name, $keyword)) {
                $needMoneyHook = false;
                unset($options->enum);
                break;
            }
        }

        if ($needMoneyHook) {
            $options->hook[] = 'BswHook\\MoneyStringify::class';
        }

        $needRateHook = false;

        foreach (['probability'] as $keyword) {
            if (strpos($item->name, $keyword) !== false) {
                $needRateHook = true;
                break;
            }
        }

        if ($needRateHook) {
            $options->hook[] = 'BswHook\\RateStringify::class';
        }

        if (strpos($item->name, 'percent') !== false) {
            $options->render = "{value} %";
        }

        // comment to label
        if ($args['comment-2-label'] == 'yes' && !empty($item->comment)) {
            $options->label = trim($item->comment);
        }

        return $options;
    }

    /**
     * Entity persistence hint
     *
     * @param object   $item
     * @param string   $table
     * @param array    $fields
     * @param array    $args
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityPersistenceHint(
        $item,
        string $table,
        array $fields,
        array $args,
        stdClass $options = null
    ) {
        $options = $options ?? new stdClass();

        $intType = [Abs::MYSQL_TINYINT, Abs::MYSQL_SMALLINT, Abs::MYSQL_INT, Abs::MYSQL_BIGINT];
        $floatType = [Abs::MYSQL_FLOAT, Abs::MYSQL_DOUBLE, Abs::MYSQL_DECIMAL];

        // numeric
        if (in_array($item->type, array_merge($intType, $floatType))) {
            $options->type = 'BswForm\\Number::class';
            if ($item->type == Abs::MYSQL_SMALLINT) {
                if ($item->unsigned) {
                    $options->typeArgs['max'] = Abs::MYSQL_SMALLINT_UNS_MAX;
                } else {
                    $options->typeArgs['min'] = Abs::MYSQL_SMALLINT_MIN;
                    $options->typeArgs['max'] = Abs::MYSQL_SMALLINT_MAX;
                }
            } elseif ($item->type == Abs::MYSQL_BIGINT) {
                if ($item->unsigned) {
                    $options->typeArgs['max'] = Abs::MYSQL_BIGINT_UNS_MAX;
                } else {
                    $options->typeArgs['min'] = Abs::MYSQL_BIGINT_MIN;
                    $options->typeArgs['max'] = Abs::MYSQL_BIGINT_MAX;
                }
            }
        }

        // json
        if ($item->type === Abs::MYSQL_JSON) {
            $options->hook[] = 'BswHook\JsonStringify::class';
        }

        // select
        if ($item->type == Abs::MYSQL_TINYINT && self::enumIsExists($table, $item, $args)) {
            $options->type = 'BswForm\\Select::class';
            $options->enum = true;
        }

        // enum map to field
        $map = [];
        if ($enum = $map[$item->name] ?? false) {
            $options->enum = $enum;
        }

        // no show
        if (in_array($item->name, ['add_time', 'update_time', 'password', 'cdkey', 'google_auth_secret'])) {
            $options->show = false;
        }

        if (in_array($item->name, ['password'])) {
            $options->ignoreBlank = true;
        }

        if ($item->length > 64) {
            $options->type = 'BswForm\\TextArea::class';
        }

        if (!in_array($item->type, $intType) && !in_array($item->type, $floatType) && empty($item->length)) {
            $options->type = 'BswForm\\TextArea::class';
        }

        // upload file
        if (strpos($item->name, '_attachment_id') !== false) {
            $options->type = 'BswForm\\Upload::class';
            $options->typeArgs['flag'] = str_replace('_', '-', strtolower($table));
            $options->typeArgs['file_md5_key'] = 'md5';
            $options->typeArgs['file_sha1_key'] = 'sha1';
        }

        if (in_array($item->name, ['md5'])) {
            $options->disabled = true;
        }

        if ($item->name == 'route_name') {
            $options->type = 'BswForm\Select::class';
            $options->enumExtra = true;
        }

        // hook Timestamp
        if (
            (static::relationWithKeyword($item->name, 'time') || static::relationWithKeyword($item->name, 'period')) &&
            strpos($item->type, 'int') !== false
        ) {
            $options->hook[] = 'BswHook\\Timestamp::class';
            $options->type = 'BswForm\\Datetime::class';
        }

        // Y-m-d H:i:s
        if ($item->type === 'timestamp' || $item->type === 'datetime') {
            $options->type = 'BswForm\\Datetime::class';
        }

        // H:i:s
        if ($item->type === 'time') {
            $options->type = 'BswForm\\Time::class';
        }

        // Y-m-d
        if ($item->type === 'date') {
            $options->type = 'BswForm\\Date::class';
        }

        $needMoneyHook = false;

        foreach (['money', 'amount', 'paid', 'cost', 'price', 'balance'] as $keyword) {
            if (static::relationWithKeyword($item->name, $keyword)) {
                $needMoneyHook = true;
                break;
            }
        }

        foreach (['score', 'percent', 'times', 'count'] as $keyword) {
            if (static::relationWithKeyword($item->name, $keyword)) {
                $needMoneyHook = false;
                unset($options->enum);
                break;
            }
        }

        if ($needMoneyHook) {
            $options->hook[] = 'BswHook\\Money::class';
            $options->typeArgs['step'] = 0.01;
        }

        $needRateHook = false;

        foreach (['probability'] as $keyword) {
            if (strpos($item->name, $keyword) !== false) {
                $needRateHook = true;
                break;
            }
        }

        if ($needRateHook) {
            $options->hook[] = 'BswHook\\Rate::class';
            $options->typeArgs['step'] = 0.01;
        }

        if (strpos($item->name, 'percent') !== false) {
            unset($options->type);
        }

        // comment to label
        if ($args['comment-2-label'] == 'yes' && !empty($item->comment)) {
            $options->label = trim($item->comment);
        }

        return $options;
    }

    /**
     * Entity filter hint
     *
     * @param object   $item
     * @param string   $table
     * @param array    $fields
     * @param array    $args
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityFilterHint($item, string $table, array $fields, array $args, stdClass $options = null)
    {
        $options = $options ?? new stdClass();
        $options = self::entityPersistenceHint($item, $table, $fields, $args, $options);

        if (in_array($item->name, ['add_time', 'update_time'])) {
            unset($options->show);
        }

        if (isset($options->type)) {
            if ($options->type == 'BswForm\\Upload::class') {
                $options->type = 'BswForm\\Number::class';
                unset($options->typeArgs);
            }
            if ($options->type == 'BswForm\\TextArea::class') {
                unset($options->type);
            }
        }

        if (isset($options->ignoreBlank)) {
            unset($options->ignoreBlank);
        }

        // Y-m-d H:i:s
        if ($item->type === 'timestamp' || $item->type === 'datetime') {
            $options->column = 4;
            $options->filter = 'BswFilter\\Between::class';
            $options->type = 'BswForm\\DatetimeRange::class';
        }

        if (isset($options->hook) && in_array('BswHook\\Timestamp::class', $options->hook)) {
            $options->column = 4;
            $options->filter = 'BswFilter\\Between::class';
            $options->filterArgs = ['timestamp' => true];
            $options->type = 'BswForm\\DatetimeRange::class';
            unset($options->hook);
        }

        if (strpos($item->name, '_day')) {
            $options->filter = 'BswFilter\\Accurate::class';
        }

        unset(
            $options->hide,
            $options->disabled,
            $options->rules,
            $options->tips,
            $options->html,
            $options->validatorType
        );

        return $options;
    }

    /**
     * Entity mixed hint
     *
     * @param object   $item
     * @param string   $table
     * @param array    $fields
     * @param array    $args
     * @param stdClass $options
     *
     * @return stdClass
     */
    public static function entityMixedHint($item, string $table, array $fields, array $args, stdClass $options = null)
    {
        $options = $options ?? new stdClass();
        if (strpos($item->type, 'int') !== false) {
            $options->sort = true;
        }

        return $options;
    }

    /**
     * @param object $item
     * @param string $table
     * @param array  $fields
     *
     * @return mixed
     */
    public static function previewTailorHint($item, string $table, array $fields)
    {
        $imageFields = [
            'icon_attachment_id',
            'image_attachment_id',
            'avatar_attachment_id',
            'media_attachment_id',
            'screen_attachment_id',
            'tag_attachment_id',
            'cover_attachment_id',
            'photo_attachment_id',
        ];

        if (in_array($item->name, $imageFields)) {
            return 'Tailor\\AttachmentImage::class';
        }

        if (strpos($item->name, 'attachment_id') !== false) {
            return 'Tailor\\AttachmentLink::class';
        }

        if ($item->name === 'deep' && isset($fields['filename'])) {
            return ['Tailor\\AttachmentFile::class', ['deep', 'filename']];
        }

        return null;
    }

    /**
     * @param object $item
     * @param string $table
     * @param array  $fields
     *
     * @return mixed
     */
    public static function persistenceTailorHint($item, string $table, array $fields)
    {
        if ($item->name === 'password') {
            return 'Tailor\\NewPassword::class';
        }

        return null;
    }
}