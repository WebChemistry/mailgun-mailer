<?php declare(strict_types = 1);

namespace WebChemistry\MailGunMailer\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use WebChemistry\MailGunMailer\MailGunMailer;

final class MailGunExtension extends CompilerExtension {

	public function getConfigSchema(): Schema {
		return Expect::structure([
			'debug' => Expect::bool(false),
			'endPoint' => Expect::string('https://api.eu.mailgun.net'),
			'domain' => Expect::string()->required(),
			'apiKey' => Expect::string()->required(),
		]);
	}

	public function beforeCompile(): void {
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		if (!$builder->hasDefinition('nette.mailer')) {
			$def = $builder->addDefinition($this->prefix('mailer'));
		} else {
			$def = $builder->getDefinition('nette.mailer');
		}

		$def->setFactory(MailGunMailer::class, [
			'debug' => $config->debug,
			'endPoint' => $config->endPoint,
			'domain' => $config->domain,
			'apiKey' => $config->apiKey,
		]);
	}

}
