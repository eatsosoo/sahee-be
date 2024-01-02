<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * ERR_RECORD_NOT_FOUND,
 *
 * ERR_CANNOT_SAVE_TO_DB,
 *
 * ERR_CANNOT_CREATE_RECORD,
 * ERR_CANNOT_UPDATE_RECORD,
 * ERR_CANNOT_DELETE_RECORD,
 * ERR_CANNOT_CREATE_RELATED_DATA,
 * ERR_CANNOT_UPDATE_RELATED_RECORDS,
 * ERR_CANNOT_DELETE_RELATED_RECORDS,
 *
 * ERR_INVALID_DATETIME_DATA,
 * ERR_DATE_RANGE_INFO,
 * ERR_PAGINATION,
 *
 * ERR_ACTION_FAIL,
 * ERR_ID_IS_NOT_PROVIDED,
 * ERR_MODEL_CLASS_NOT_EXISTS,
 * ERR_REQUEST_DATA_IS_INVALID,
 * ERR_INTERNAL_SERVER_ERROR,
 */
final class ErrorCodes extends Enum
{
    const ERR_RECORD_NOT_FOUND = 'Record not found';

    const ERR_CANNOT_SAVE_TO_DB = 'Can not save to database';

    const ERR_CANNOT_CREATE_RECORD = 'Can not create the record';
    const ERR_CANNOT_UPDATE_RECORD = 'Can not update the record';
    const ERR_CANNOT_DELETE_RECORD = 'Can not delete this record';
    const ERR_CANNOT_CREATE_RELATED_DATA = 'Can not create related records';
    const ERR_CANNOT_UPDATE_RELATED_RECORDS = 'Can not update related records';
    const ERR_CANNOT_DELETE_RELATED_RECORDS = 'Can not remove related records';

    const ERR_INVALID_DATETIME_DATA = 'Invalid datetime data';
    const ERR_DATE_RANGE_INFO = 'Invalid range of date';
    const ERR_PAGINATION = 'Invalid pagination';

    const ERR_ACTION_FAIL = 'Cannot execute the action';
    const ERR_ID_IS_NOT_PROVIDED = 'ID is not provided';
    const ERR_MODEL_CLASS_NOT_EXISTS = 'Model class is not exists';
    const ERR_REQUEST_DATA_IS_INVALID = 'Request data is invalid';
    const ERR_INTERNAL_SERVER_ERROR = 'Internal server error';

    const ERR_CANNOT_USE_MAIL_BLOCK = '編集中のメールブロックのため、使用できません。';
    const ERR_CANNOT_DELETE_MAIL_BLOCK = 'メールブロックは使用中のため削除できません';
}
