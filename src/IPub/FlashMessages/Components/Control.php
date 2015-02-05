<?php
/**
 * Control.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:FlashMessages!
 * @subpackage	Components
 * @since		5.0
 *
 * @date		12.03.14
 */

namespace IPub\FlashMessages\Components;

use Nette;
use Nette\Application;
use Nette\Localization;
use Nette\Utils;

use IPub;
use IPub\FlashMessages;
use IPub\FlashMessages\Exceptions;

/**
 * Flash messages control
 *
 * @package		iPublikuj:FlashMessages!
 * @subpackage	Components
 *
 * @property-read Application\UI\ITemplate $template
 */
class Control extends Application\UI\Control
{
	/**
	 * @var string
	 */
	protected $templatePath;

	/**
	 * @var Localization\ITranslator
	 */
	protected $translator;

	/**
	 * @param Localization\ITranslator $translator
	 */
	public function injectTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * Render control
	 */
	public function render()
	{
		// Check if control has template
		if ($this->template instanceof Nette\Bridges\ApplicationLatte\Template) {
			// Assign snippets to template
			$this->template->flashes = $this->parent->getTemplate()->flashes;

			// Check if translator is available
			if ($this->getTranslator() instanceof Localization\ITranslator) {
				$this->template->setTranslator($this->getTranslator());
			}

			// If template was not defined before...
			if ($this->template->getFile() === NULL) {
				// ...try to get base component template file
				$templatePath = !empty($this->templatePath) ? $this->templatePath : __DIR__ . DIRECTORY_SEPARATOR .'template'. DIRECTORY_SEPARATOR .'default.latte';
				$this->template->setFile($templatePath);
			}

			// Render component template
			$this->template->render();

		} else {
			throw new Exceptions\InvalidStateException('Flash messages control is without template.');
		}
	}

	/**
	 * Change default control template path
	 *
	 * @param string $templatePath
	 *
	 * @return $this
	 *
	 * @throws Exceptions\FileNotFoundException
	 */
	public function setTemplateFile($templatePath)
	{
		// Check if template file exists...
		if (!is_file($templatePath)) {
			// ...check if extension template is used
			if (is_file(__DIR__ . DIRECTORY_SEPARATOR .'template'. DIRECTORY_SEPARATOR . $templatePath)) {
				$templatePath = __DIR__ . DIRECTORY_SEPARATOR .'template'. DIRECTORY_SEPARATOR . $templatePath;

			} else {
				// ...if not throw exception
				throw new Exceptions\FileNotFoundException('Template file "'. $templatePath .'" was not found.');
			}
		}

		$this->templatePath = $templatePath;

		return $this;
	}

	/**
	 * @param Localization\ITranslator $translator
	 *
	 * @return $this
	 */
	public function setTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;

		return $this;
	}

	/**
	 * @return Localization\ITranslator|null
	 */
	public function getTranslator()
	{
		if ($this->translator instanceof Localization\ITranslator) {
			return $this->translator;
		}

		return NULL;
	}
}