<?php
declare(strict_types=1);


/**
 * Nextcloud - Social Support
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2018, Maxence Lange <maxence@artificial-owl.com>
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


namespace OC\Entities\Command;


use daita\NcSmallPhpTools\Exceptions\ShellMissingItemException;
use daita\NcSmallPhpTools\Exceptions\ShellUnknownCommandException;
use daita\NcSmallPhpTools\Exceptions\ShellUnknownItemException;
use daita\NcSmallPhpTools\IInteractiveShellClient;
use daita\NcSmallPhpTools\Service\InteractiveShell;
use daita\NcSmallPhpTools\Traits\TStringTools;
use Exception;
use OC\Entities\Exceptions\EntityAccountNotFoundException;
use OC\Entities\Exceptions\EntityTypeNotFoundException;
use OCP\Entities\Helper\IEntitiesHelper;
use OCP\Entities\IEntitiesManager;
use OCP\Entities\Implementation\IEntities\IEntities;
use OCP\Entities\Model\IEntity;
use OCP\Entities\Model\IEntityAccount;
use OCP\Entities\Model\IEntityMember;
use OCP\Entities\Model\IEntityType;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class Session
 *
 * @package OC\Entities\Command
 */
class Session extends ExtendedBase implements IInteractiveShellClient {


	use TStringTools;


	/** @var IEntitiesManager */
	private $entitiesManager;

	/** @var IEntitiesHelper */
	private $entitiesHelper;


	/** @var IEntityAccount */
	private $viewer;


	/**
	 * Session constructor.
	 *
	 * @param IEntitiesManager $entitiesManager
	 * @param IEntitiesHelper $entitiesHelper
	 */
	public function __construct(IEntitiesManager $entitiesManager, IEntitiesHelper $entitiesHelper
	) {
		parent::__construct();

		$this->entitiesManager = $entitiesManager;
		$this->entitiesHelper = $entitiesHelper;
	}


	/**
	 *
	 */
	protected function configure() {
		parent::configure();
		$this->setName('entities:session')
			 ->addArgument(
				 'viewer', InputArgument::OPTIONAL, 'session from a user\'s point of view',
				 ''
			 )
			 ->addOption(
				 'visibility', '', InputOption::VALUE_REQUIRED, 'level of visibility (as admin)',
				 'none'
			 )
			 ->addOption(
				 'non-admin-viewer', '', InputOption::VALUE_NONE,
				 'create a non-admin temporary viewer'
			 )
			 ->setDescription('Start session as a temporary (or local) user');
	}


	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @throws Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {

		$this->input = $input;
		$this->output = $output;

		$this->generateViewer();
		$this->entitiesManager->setViewer($this->viewer);

		$this->output->writeln('* Identity used during this session:');
		$this->outputAccount($this->viewer);
		$this->output->writeln('');

		$interactiveShell = new InteractiveShell($this, $input, $output, $this);
		$interactiveShell->setCommands(
			[
				'create.entity.?type_IEntities',
				'create.account.?type_IEntitiesAccounts',
				'list.entities.?type_IEntities',
				'list.accounts.?type_IEntitiesAccounts',
				'search.account',
				'details.?entity_id',
				'invite.?account_account.?entity_id',
				'join.?entity_id',
				'notifications'
			]
		);

		$interactiveShell->run(
			'EntitiesManager [<info>' . $this->viewer->getAccount()
			. '</info>]:<comment>%PATH%</comment>> '
		);

	}


	/**
	 * @param string $source
	 * @param string $needle
	 *
	 * @return string[]
	 */
	public function fillCommandList(string $source, string $needle): array {

		switch ($source) {
			case 'type':
				$entries = $this->entitiesHelper->getEntityTypes($needle);

				return array_map(
					function(IEntityType $entry) {
						return $entry->getType();
					}, $entries
				);

			case 'entity':
				$entries = $this->entitiesManager->getAllEntities();

				return array_map(
					function(IEntity $entry) {
						return $entry->getId();
					}, $entries
				);

		}

		return [];
	}


	/**
	 * @param string $command
	 *
	 * @throws ShellMissingItemException
	 * @throws ShellUnknownItemException
	 * @throws ShellUnknownCommandException
	 */
	public function manageCommand(string $command): void {
		$args = explode(' ', $command);
		$cmd = array_shift($args);
		switch ($cmd) {

			case 'create':
				$this->manageCommandCreate($args);
				break;

			case 'list':
				$this->manageCommandList($args);
				break;

			case 'search':
				$this->manageCommandSearch($args);
				break;

			case 'details':
				$this->manageCommandDetails($args);
				break;

			case 'invite':
				$this->manageCommandInvite($args);
				break;

			case 'join':
				$this->manageCommandJoin($args);
				break;

			case 'notifications':
				$this->manageCommandNotifications($args);
				break;

			default:
				throw new ShellUnknownCommandException();
		}
	}



	/**
	 * @param array $args
	 *
	 * @throws ShellMissingItemException
	 * @throws ShellUnknownItemException
	 */
	private function manageCommandCreate(array $args) {
		$item = array_shift($args);
		switch ($item) {
			case 'entity':
				$this->manageCommandCreateEntity($args);
				break;

			case 'account':
				$this->manageCommandCreateAccount($args);
				break;

			case '':
				throw new ShellMissingItemException();

			default:
				throw new ShellUnknownItemException();
		}
	}


	/**
	 * @param array $args
	 *
	 * @throws ShellUnknownItemException
	 * @throws ShellMissingItemException
	 */
	private function manageCommandCreateEntity(array $args) {
		$type = array_shift($args);
		if (!is_string($type)) {
			throw new ShellMissingItemException();
		}

		try {
			$this->verifyEntityType($type);
		} catch (EntityTypeNotFoundException $e) {
			throw new ShellUnknownItemException();
		}
	}


	/**
	 * @param array $args
	 *
	 * @throws ShellMissingItemException
	 */
	private function manageCommandCreateAccount(array $args) {
		echo '########## create account: ' . json_encode($args) . "\n";
		throw new ShellMissingItemException();
	}


	/**
	 * @param array $args
	 */
	private function manageCommandSearch(array $args) {
		$this->output->writeln($args);
	}


	/**
	 * @throws EntityAccountNotFoundException
	 * @throws Exception
	 */
	private function generateViewer(): void {
		$viewerName = $this->input->getArgument('viewer');
		$level = $this->input->getOption('visibility');

		if ($viewerName === '') {
			$asAdmin = !$this->input->getOption('non-admin-viewer');
			$this->viewer = $this->entitiesHelper->temporaryLocalAccount($asAdmin);
		} else {
			$this->viewer = $this->entitiesHelper->getLocalAccount($viewerName);
		}

		$listLevel = array_values(IEntityMember::CONVERT_LEVEL);
		if (!in_array($level, $listLevel)) {
			throw new Exception(
				'must specify an Visibility Level (--visibility): ' . implode(', ', $listLevel)
			);
		}

		$this->viewer->getOptions()
					 ->setOptionInt(
						 'viewer.visibility', array_search($level, IEntityMember::CONVERT_LEVEL)
					 );
	}


	/**
	 * @param string $type
	 *
	 * @throws EntityTypeNotFoundException
	 */
	private function verifyEntityType(string $type) {
		$all = $this->entitiesHelper->getEntityTypes(IEntities::INTERFACE);
		foreach ($all as $entityType) {
			if ($entityType->getType() === $type) {
				return;
			}
		}

		throw new EntityTypeNotFoundException();
	}

	private function manageCommandList(array $args) {
	}

	private function manageCommandDetails(array $args) {
	}

	private function manageCommandInvite(array $args) {
	}

	private function manageCommandJoin(array $args) {
	}

	private function manageCommandNotifications(array $args) {
	}
}

