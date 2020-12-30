<?php declare(strict_types = 1);

namespace WebChemistry\MailGunMailer;

use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Mailgun;
use Mailgun\Message\MessageBuilder;
use Mailgun\Model\Message\SendResponse;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\SmartObject;

final class MailGunMailer implements Mailer {

	use SmartObject;

	/** @var Mailgun */
	private $client;

	/** @var string */
	private $domain;

	public function __construct(string $apiKey, string $domain, string $endPoint, bool $debug = false) {
		$configurator = new HttpClientConfigurator();
		$configurator->setEndpoint($endPoint);
		$configurator->setDebug($debug);
		$configurator->setApiKey($apiKey);

		$this->client = new Mailgun($configurator, new ModelHydrator());
		$this->domain = $domain;
	}

	public function createBuilderFromMessage(Message $mail): MessageBuilder {
		$builder = new MessageBuilder();
		foreach ($mail->getFrom() as $email => $name) {
			$builder->setFromAddress($email, [
				'full_name' => $name,
			]);
		}

		$builder->setSubject($mail->getSubject());

		if ($mail->getHtmlBody()) {
			$builder->setHtmlBody($mail->getHtmlBody());
		} else {
			$builder->setTextBody($mail->getBody());
		}

		foreach ($mail->getHeader('To') as $email => $name) {
			$builder->addToRecipient($email, [
				'full_name' => $name,
			]);
		}

		return $builder;
	}

	public function sendFromBuilder(MessageBuilder $builder): SendResponse {
		return $this->client->messages()->send($this->domain, $builder->getMessage());
	}

	public function send(Message $mail): void {
		$this->sendFromBuilder($this->createBuilderFromMessage($mail));
	}

}
