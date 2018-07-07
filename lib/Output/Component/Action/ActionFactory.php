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

use SR\Console\Output\Exception\InvalidArgumentException;

final class ActionFactory
{
    /**
     * @var string[]
     */
    private const action_types = [
        SimpleAction::class,
        BracketedAction::class,
    ];

    /**
     * @param string|null $type
     * @param mixed       ...$constructorArguments
     *
     * @return AbstractAction
     */
    public static function create(string $type = null, ...$constructorArguments): AbstractAction
    {
        $type = self::resolveQualifiedActionTypeClass($type);

        return new $type(...$constructorArguments);
    }

    /**
     * @param string|null $type
     *
     * @return string
     */
    private static function resolveQualifiedActionTypeClass(string $type = null): string
    {
        $type = empty($type) ? SimpleAction::class : ucfirst($type);
        $fqns = [$type, sprintf('%s\%s', __NAMESPACE__, $type), sprintf('%s\%sAction', __NAMESPACE__, $type)];

        foreach ($fqns as $qualified) {
            if (self::isValidActionType($qualified)) {
                return $qualified;
            }
        }

        throw new InvalidArgumentException('Unable to find action of type class (none of %s are valid action types).', implode(', ', array_map(function (string $type): string {
            return sprintf('"%s"', $type);
        }, $fqns)));
    }

    /**
     * @param string|null $type
     *
     * @return bool
     */
    private static function isValidActionType(string $type = null): bool
    {
        return in_array($type, self::action_types, true) && class_exists($type);
    }
}
