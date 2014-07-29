<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams {
    use stubbles\streams\file\FileInputStream;

    function linesOf($input)
    {
        return new InputStreamIterator(FileInputStream::castFrom($input));
    }
}
