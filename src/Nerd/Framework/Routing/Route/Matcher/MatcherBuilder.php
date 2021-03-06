<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 11/26/16
 * Time: 5:44 PM
 */

namespace Nerd\Framework\Routing\Route\Matcher;

use Nerd\Framework\Routing\RouterException;

class MatcherBuilder
{
    /**
     * @param string $routePattern
     * @return Matcher
     */
    public function build(string $routePattern): Matcher
    {
        $this->validate($routePattern);

        if ($routePattern[0] == '~') {
            return new RegexMatcher($routePattern);
        }

        if ($this->isRouteWithoutParameters($routePattern)) {
            return new StaticMatcher($routePattern);
        }

        $parts = explode('/', $routePattern);

        foreach ($parts as $part) {
            if (strrpos($part, ':') > 0 || strrpos($part, '&') > 0) {
                return new ExtendedMatcher($routePattern);
            }
        }

        return new FastMatcher($routePattern);
    }

    /**
     * @param string $routePattern
     * @throws RouterException
     */
    private function validate(string $routePattern)
    {
        if (empty($routePattern)) {
            throw new RouterException("Route could not be empty.");
        }

        if ($routePattern != '/' && $routePattern[0] == '/') {
            throw new RouterException("Route could not start with '/' character.");
        }
    }

    /**
     * @param string $routePattern
     * @return bool
     */
    private function isRouteWithoutParameters(string $routePattern): bool
    {
        return strpos($routePattern, ':') === false && strpos($routePattern, '&') === false;
    }
}
