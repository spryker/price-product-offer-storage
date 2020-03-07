<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Oauth\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\OauthRefreshTokenTransfer;
use Generated\Shared\Transfer\SpyOauthAccessTokenEntityTransfer;
use Generated\Shared\Transfer\SpyOauthClientEntityTransfer;
use Generated\Shared\Transfer\SpyOauthScopeEntityTransfer;

interface OauthEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\SpyOauthAccessTokenEntityTransfer $spyOauthAccessTokenEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthAccessTokenEntityTransfer
     */
    public function saveAccessToken(SpyOauthAccessTokenEntityTransfer $spyOauthAccessTokenEntityTransfer): SpyOauthAccessTokenEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\OauthRefreshTokenTransfer $oauthRefreshTokenTransfer
     *
     * @return \Generated\Shared\Transfer\OauthRefreshTokenTransfer
     */
    public function saveRefreshToken(OauthRefreshTokenTransfer $oauthRefreshTokenTransfer): OauthRefreshTokenTransfer;

    /**
     * @param \Generated\Shared\Transfer\SpyOauthClientEntityTransfer $spyOauthClientEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthClientEntityTransfer
     */
    public function saveClient(SpyOauthClientEntityTransfer $spyOauthClientEntityTransfer): SpyOauthClientEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\SpyOauthScopeEntityTransfer $spyOauthScopeEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthScopeEntityTransfer
     */
    public function saveScope(SpyOauthScopeEntityTransfer $spyOauthScopeEntityTransfer): SpyOauthScopeEntityTransfer;

    /**
     * @param string $identifier
     *
     * @return void
     */
    public function deleteAccessTokenByIdentifier(string $identifier): void;

    /**
     * @param \Generated\Shared\Transfer\OauthRefreshTokenTransfer $oauthRefreshTokenTransfer
     *
     * @return void
     */
    public function revokeRefreshToken(OauthRefreshTokenTransfer $oauthRefreshTokenTransfer): void;

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\OauthRefreshTokenTransfer[] $oauthRefreshTokenTransfers
     *
     * @return void
     */
    public function revokeAllRefreshTokens(ArrayObject $oauthRefreshTokenTransfers): void;

    /**
     * @param string $expiresAt
     *
     * @return int
     */
    public function deleteExpiredRefreshTokens(string $expiresAt): int;
}
