<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Component\Action;

interface ActionStates
{
    /**
     * @var string
     */
    public const STATE_INACTIVE = 'inactive';

    /**
     * @var string
     */
    public const STATE_PREFIX = 'prefix';

    /**
     * @var string
     */
    public const STATE_ACTION = 'action';

    /**
     * @var string
     */
    public const STATE_STATUS_TEXT_ACTIVE = 'status-text-active';

    /**
     * @var string
     */
    public const STATE_STATUS_TEXT_INACTIVE = 'status-text-inactive';

    /**
     * @var string
     */
    public const STATE_STATUS_PROGRESS_ACTIVE = 'status-progress-active';

    /**
     * @var string
     */
    public const STATE_STATUS_PROGRESS_INACTIVE = 'status-progress-inactive';

    /**
     * @var string
     */
    public const STATE_RESULT = 'result';

    /**
     * @var string
     */
    public const STATE_EXTRAS_TEXT_ACTIVE = 'extras-text-active';

    /**
     * @var string
     */
    public const STATE_EXTRAS_TEXT_INACTIVE = 'extras-text-inactive';
}
