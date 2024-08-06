<?php

namespace Vanguard\UserActivity\Support\Enum;

namespace Vanguard\UserActivity\Support\Enum;

class ActivityTypes
{
    const NEW_PERMISSION = 'new_permission';
    const UPDATED_PERMISSION = 'updated_permission';
    const DELETED_PERMISSION = 'deleted_permission';

    const NEW_ROLE = 'new_role';
    const UPDATED_ROLE = 'updated_role';
    const DELETED_ROLE = 'deleted_role';
    const UPDATED_ROLE_PERMISSIONS = 'updated_role_permissions';

    const LOGGED_IN = 'logged_in';
    const LOGGED_OUT = 'logged_out';
    const CREATED_ACCOUNT = 'created_account';
    const UPDATED_AVATAR = 'updated_avatar';
    const UPDATED_PROFILE = 'updated_profile';
    const DELETED_USER = 'deleted_user';
    const BANNED_USER = 'banned_user';
    const UPDATED_PROFILE_DETAILS_FOR = 'updated_profile_details_for';
    const CREATED_ACCOUNT_FOR = 'created_account_for';
    const UPDATED_SETTINGS = 'updated_settings';
    const ENABLED_2FA = 'enabled_2fa';
    const DISABLED_2FA = 'disabled_2fa';
    const ENABLED_2FA_FOR = 'enabled_2fa_for';
    const DISABLED_2FA_FOR = 'disabled_2fa_for';
    const REQUESTED_PASSWORD_RESET = 'requested_password_reset';
    const RESETED_PASSWORD = 'reseted_password';

    const STARTED_IMPERSONATING = 'started_impersonating';
    const STOPPED_IMPERSONATING = 'stopped_impersonating';
}