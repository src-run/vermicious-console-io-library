<?php

/*
 * This file is part of the `src-run/vermicious-console-io-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Console\Output\Helper;

use SR\Console\Output\Style\StyleAwareTrait;
use SR\Console\Output\Style\StyleInterface;
use Symfony\Component\Console\Application;

class TitleHelper
{
    use StyleAwareTrait;

    /**
     * @param StyleInterface $style
     */
    public function __construct(StyleInterface $style)
    {
        $this->setStyle($style);
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function title(string $title): self
    {
        $lines = [vsprintf('%s <em>%s</em>', [
            (new DecorationHelper('black', null, 'bold'))->decorate('-'),
            $title,
        ])];

        $this->io->prependBlock();
        $this->io->separator();
        $this->io->writeln($lines);
        $this->io->separator();
        $this->io->newline();

        return $this;
    }

    /**
     * @param Application $application
     * @param array       ...$properties
     *
     * @return self
     */
    public function applicationTitle(Application $application, ...$properties): self
    {
        $this->io->prependBlock();
        $this->io->separator();
        $this->io->environment(StyleInterface::VERBOSITY_VERBOSE)->separator(1);
        $this->io->writeln([vsprintf('%s <em>%s (%s) %s</em>', [
            (new DecorationHelper('black', null, 'bold'))->decorate('-'),
            $application->getName(),
            $this->getApplicationVersion($application),
            $this->getApplicationGitHash($application),
        ])]);

        if (0 < count($properties = $this->compileApplicationProps($application, $properties))) {
            $this->io->separator(1);
            $this->io->writeln($properties);
        }

        $this->io->environment(StyleInterface::VERBOSITY_VERBOSE)->separator(1);
        $this->io->separator();
        $this->io->newline();

        return $this;
    }

    /**
     * @param Application $application
     * @param array       $properties
     *
     * @return string[]
     */
    private function compileApplicationProps(Application $application, array $properties): array
    {
        if (0 < count($properties = $this->prependAutoApplicationProps($application, $properties))) {
            $len = max(array_map(function ($name) {
                return strlen($name);
            }, array_keys($properties)));

            array_walk($properties, function (&$prop, $name) use ($len) {
                $prop = vsprintf('%s %s %s', [
                    (new DecorationHelper('black', null, 'bold'))->decorate('-'),
                    $this->io->pad('@'.$name, $len+1, ' ', STR_PAD_RIGHT),
                    $prop,
                ]);
            });
        }

        return $properties;
    }

    /**
     * @param Application $application
     * @param array       $properties
     *
     * @return array
     */
    private function prependAutoApplicationProps(Application $application, array $properties): array
    {
        if (!isset($properties['author']) && null !== $author = $this->getApplicationAuthor($application)) {
            $properties['author'] = $author;
        }

        if (!isset($properties['license']) && null !== $license = $this->getApplicationLicense($application)) {
            $properties['license'] = $license;
        }

        return $properties;
    }

    /**
     * @param Application $application
     *
     * @return string
     */
    private function getApplicationLicense(Application $application): ?string
    {
        $license = null;

        if (method_exists($application, 'getLicense')) {
            $license = $application->getLicense();

            if (method_exists($application, 'getLicenseLink')) {
                $license = sprintf('%s <%s>', $license, $application->getLicenseLink());
            }
        }

        return $license;
    }

    /**
     * @param Application $application
     *
     * @return string
     */
    private function getApplicationAuthor(Application $application): ?string
    {
        $author = null;

        if (method_exists($application, 'getAuthor')) {
            $author = $application->getAuthor();

            if (method_exists($application, 'getAuthorEmail')) {
                $author = sprintf('%s <%s>', $author, $application->getAuthorEmail());
            }
        }

        return $author;
    }

    /**
     * @param Application $application
     *
     * @return string
     */
    private function getApplicationGitHash(Application $application): string
    {
        if (method_exists($application, 'getGitHash') && $application->getGitHash()) {
            return sprintf('[%s]', $application->getGitHash());
        }

        return '';
    }

    /**
     * @param Application $application
     *
     * @return string
     */
    private function getApplicationVersion(Application $application): string
    {
        if (null !== $version = $application->getVersion()) {
            return substr($version, 0, 1) === 'v' ? $version : sprintf('v%s', $version);
        }

        return 'master';
    }
}

/* EOF */
