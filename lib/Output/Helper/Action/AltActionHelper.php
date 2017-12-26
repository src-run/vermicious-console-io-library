<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper\Action;

use SR\Console\Output\Helper\Style\DecorationHelper;

final class AltActionHelper extends AbstractActionHelper
{
    /**
     * @param \Closure|null $formatter
     *
     * @return AbstractActionHelper
     */
    public function setActionFormatter(\Closure $formatter = null): AbstractActionHelper
    {
        return parent::setActionFormatter($formatter ?? function (string $action, DecorationHelper $decorator = null) {
            return sprintf(' %s ... ', ($decorator ?? new DecorationHelper())->decorate(
                sprintf('[ %s ]', $action)
            ));
        });
    }

    /**
     * @param \Closure|null $formatter
     *
     * @return AbstractActionHelper
     */
    public function setResultFormatter(\Closure $formatter = null): AbstractActionHelper
    {
        return parent::setResultFormatter($formatter ?? function (string $result, DecorationHelper $decorator = null) {
            return ($decorator ?? new DecorationHelper('black', 'white'))->decorate(
                sprintf(' %s ', strtoupper($result))
            );
        });
    }
}
