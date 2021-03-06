<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams;
/**
 * Interface for stream factories.
 */
interface StreamFactory
{
    /**
     * creates an input stream for given source
     *
     * @param   mixed  $source   source to create input stream from
     * @param   array  $options  list of options for the input stream
     * @return  \stubbles\streams\InputStream
     */
    public function createInputStream($source, array $options = []);

    /**
     * creates an output stream for given target
     *
     * @param   mixed  $target   target to create output stream for
     * @param   array  $options  list of options for the output stream
     * @return  \stubbles\streams\OutputStream
     */
    public function createOutputStream($target, array $options = []);
}
