<?php declare(strict_types = 1);

namespace WebChemistry\MailGunMailer;

use Mailgun\Exception\HydrationException;
use Mailgun\Hydrator\Hydrator;
use Mailgun\Model\ApiResponse;
use Psr\Http\Message\ResponseInterface;

final class ModelHydrator implements Hydrator {

	/**
	 * @return ResponseInterface
	 */
	public function hydrate(ResponseInterface $response, string $class)
	{
		$body = $response->getBody()->__toString();

		$data = json_decode($body, true);

		if (JSON_ERROR_NONE !== json_last_error()) {
			throw new HydrationException(sprintf('Error (%d) when trying to json_decode response', json_last_error()));
		}

		if (is_subclass_of($class, ApiResponse::class)) {
			$object = call_user_func($class.'::create', $data);
		} else {
			$object = new $class($data);
		}

		return $object;
	}

}
