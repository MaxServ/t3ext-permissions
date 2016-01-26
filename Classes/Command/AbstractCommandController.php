<?php
namespace MaxServ\YamlConfiguration\Command;

/**
 *  Copyright notice
 *
 *  ⓒ 2016 Michiel Roos <michiel@maxserv.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is free
 *  software; you can redistribute it and/or modify it under the terms of the
 *  GNU General Public License as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful, but
 *  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *  or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 *  more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

// I can haz color / use unicode?
if (DIRECTORY_SEPARATOR !== '\\') {
    define('USE_COLOR', function_exists('posix_isatty') && posix_isatty(STDOUT));
    define('UNICODE', true);
} else {
    define('USE_COLOR', getenv('ANSICON') !== false);
    define('UNICODE', false);
}

// Get terminal width
if (@exec('tput cols')) {
    define('TERMINAL_WIDTH', exec('tput cols'));
} else {
    define('TERMINAL_WIDTH', 79);
}

/**
 * Abstract Command Controller
 *
 * @package B13\DamFalmigration
 * @subpackage Controller
 */
class AbstractCommandController extends CommandController
{

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(
        \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
        $this->configuration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
    }

    /**
     * Get module configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Output FlashMessage
     *
     * @param FlashMessage $message
     *
     * @return void
     */
    public function outputMessage($message = null)
    {
        if ($message->getTitle()) {
            $this->outputLine($message->getTitle());
        }
        if ($message->getMessage()) {
            $this->outputLine($message->getMessage());
        }
        if ($message->getSeverity() !== FlashMessage::OK) {
            $this->sendAndExit(1);
        }
    }

    /**
     * Normal message
     *
     * @param $message
     * @param boolean $flushOutput
     *
     * @return void
     */
    public function message($message = null, $flushOutput = true)
    {
        $this->outputLine($message);
        if ($flushOutput) {
            $this->response->send();
            $this->response->setContent('');
        }
    }

    /**
     * Informational message
     *
     * @param string $message
     * @param boolean $showIcon
     * @param boolean $flushOutput
     *
     * @return void
     */
    public function infoMessage($message = null, $showIcon = false, $flushOutput = true)
    {
        $icon = '';
        if ($showIcon && UNICODE) {
            $icon = '★ ';
        }
        if (USE_COLOR) {
            $this->outputLine("\033[0;36m" . $icon . $message . "\033[0m");
        } else {
            $this->outputLine($icon . $message);
        }
        if ($flushOutput) {
            $this->response->send();
            $this->response->setContent('');
        }
    }

    /**
     * Error message
     *
     * @param string $message
     * @param boolean $showIcon
     * @param boolean $flushOutput
     *
     * @return void
     */
    public function errorMessage($message = null, $showIcon = false, $flushOutput = true)
    {
        $icon = '';
        if ($showIcon && UNICODE) {
            $icon = '✖ ';
        }
        if (USE_COLOR) {
            $this->outputLine("\033[31m" . $icon . $message . "\033[0m");
        } else {
            $this->outputLine($icon . $message);
        }
        if ($flushOutput) {
            $this->response->send();
            $this->response->setContent('');
        }
    }

    /**
     * Warning message
     *
     * @param string $message
     * @param boolean $showIcon
     * @param boolean $flushOutput
     *
     * @return void
     */
    public function warningMessage($message = null, $showIcon = false, $flushOutput = true)
    {
        $icon = '';
        if ($showIcon) {
            $icon = '! ';
        }
        if (USE_COLOR) {
            $this->outputLine("\033[0;33m" . $icon . $message . "\033[0m");
        } else {
            $this->outputLine($icon . $message);
        }
        if ($flushOutput) {
            $this->response->send();
            $this->response->setContent('');
        }
    }

    /**
     * Success message
     *
     * @param string $message
     * @param boolean $showIcon
     * @param boolean $flushOutput
     *
     * @return void
     */
    public function successMessage($message = null, $showIcon = false, $flushOutput = true)
    {
        $icon = '';
        if ($showIcon && UNICODE) {
            $icon = '✔ ';
        }
        if (USE_COLOR) {
            $this->outputLine("\033[0;32m" . $icon . $message . "\033[0m");
        } else {
            $this->outputLine($icon . $message);
        }
        if ($flushOutput) {
            $this->response->send();
            $this->response->setContent('');
        }
    }

    /**
     * Info string
     *
     * @param string $string
     *
     * @return string
     */
    public function infoString($string = null)
    {
        if (USE_COLOR) {
            $string = "\033[0;36m" . $string . "\033[0m";
        }

        return $string;
    }

    /**
     * Error string
     *
     * @param string $string
     *
     * @return string
     */
    public function errorString($string = null)
    {
        if (USE_COLOR) {
            $string = "\033[0;31m" . $string . "\033[0m";
        }

        return $string;
    }

    /**
     * Warning string
     *
     * @param string $string
     *
     * @return string
     */
    public function warningString($string = null)
    {
        if (USE_COLOR) {
            $string = "\033[0;33m" . $string . "\033[0m";
        }

        return $string;
    }

    /**
     * Success string
     *
     * @param string $string
     *
     * @return string
     */
    public function successString($string = null)
    {
        if (USE_COLOR) {
            $string = "\033[0;32m" . $string . "\033[0m";
        }

        return $string;
    }


    /**
     * Show a header message
     *
     * @param $message
     * @param string $style
     * @param boolean $flushOutput
     *
     * @return void
     */
    public function headerMessage($message, $style = 'info', $flushOutput = true)
    {
        // Crop the message
        $message = substr($message, 0, TERMINAL_WIDTH);
        if (UNICODE) {
            $linePaddingLength = mb_strlen('─') * (TERMINAL_WIDTH);
            $message =
                str_pad('', $linePaddingLength, '─') . LF .
                str_pad($message, TERMINAL_WIDTH) . LF .
                str_pad('', $linePaddingLength, '─');
        } else {
            $message =
                str_pad('', TERMINAL_WIDTH, '-') . LF .
                str_pad($message, TERMINAL_WIDTH) . LF .
                str_pad('', TERMINAL_WIDTH, '-');
        }
        switch ($style) {
            case 'error':
                $this->errorMessage($message, false, $flushOutput);
                break;
            case 'info':
                $this->infoMessage($message, false, $flushOutput);
                break;
            case 'success':
                $this->successMessage($message, false, $flushOutput);
                break;
            case 'warning':
                $this->warningMessage($message, false, $flushOutput);
                break;
            default:
                $this->message($message, $flushOutput);
        }
    }

    /**
     * Show a horizontal line
     *
     * @param string $style
     * @param boolean $flushOutput
     *
     * @return void
     */
    public function horizontalLine($style = '', $flushOutput = true)
    {
        if (UNICODE) {
            $linePaddingLength = mb_strlen('─') * (TERMINAL_WIDTH);
            $message =
                str_pad('', $linePaddingLength, '─');
        } else {
            $message =
                str_pad('', TERMINAL_WIDTH, '-');
        }
        switch ($style) {
            case 'error':
                $this->errorMessage($message, false, $flushOutput);
                break;
            case 'info':
                $this->infoMessage($message, false, $flushOutput);
                break;
            case 'success':
                $this->successMessage($message, false, $flushOutput);
                break;
            case 'warning':
                $this->warningMessage($message, false, $flushOutput);
                break;
            default:
                $this->message($message, $flushOutput);
        }
    }
}
