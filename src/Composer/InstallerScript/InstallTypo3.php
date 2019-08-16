<?php
declare(strict_types=1);
namespace Typo3Console\Typo3AutoInstall\Composer\InstallerScript;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Helmut Hummel <info@helhum.io>
 *  All rights reserved
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the text file GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Composer\Script\Event as ScriptEvent;
use Composer\Semver\Constraint\EmptyConstraint;
use Helhum\TYPO3\ConfigHandling\RootConfig;
use Helhum\Typo3Console\Core\Kernel;
use Helhum\Typo3Console\Install\Action\InstallActionDispatcher;
use Typo3Console\Typo3AutoInstall\Composer\ConsoleIo;
use Helhum\Typo3Console\Mvc\Cli\CommandDispatcher;
use Helhum\Typo3Console\Mvc\Cli\ConsoleOutput;
use TYPO3\CMS\Composer\Plugin\Core\InstallerScript;

class InstallTypo3 implements InstallerScript
{
    /**
     * @param ScriptEvent $event
     * @return bool
     */
    private function shouldRun(ScriptEvent $event): bool
    {
        $typo3IsSetUp = file_exists(getenv('TYPO3_PATH_ROOT') . '/typo3conf/LocalConfiguration.php');
        return !$typo3IsSetUp && !getenv('TYPO3_IS_SET_UP');
    }

    /**
     * Call the TYPO3 setup
     *
     * @param ScriptEvent $event
     * @throws \RuntimeException
     * @return bool
     * @internal
     */
    public function run(ScriptEvent $event): bool
    {
        if (!$this->shouldRun($event)) {
            return true;
        }
        $io = $event->getIO();
        $io->writeError('');
        $io->writeError('<info>Setting up TYPO3</info>');

        $this->initializeCompatibilityLayer($event);
        $consoleIO = new ConsoleIo($io);

        $setup = new InstallActionDispatcher(
            new ConsoleOutput($consoleIO->getOutput(), $consoleIO->getInput()),
            CommandDispatcher::createFromComposerRun()
        );
        $setup->dispatch([]);

        $io->writeError('');
        $io->writeError('<info>Your TYPO3 installation is now ready to use.</info>');

        $localRepository = $event->getComposer()->getRepositoryManager()->getLocalRepository();
        $serverCommandPackage = $localRepository->findPackage('typo3-console/php-server-command', new EmptyConstraint());
        if ($serverCommandPackage !== null && !getenv('DDEV_PROJECT')) {
            $io->writeError('');
            $io->writeError(sprintf('<info>Run</info> <comment>%s server:run</comment> <info>in your project root directory, to start the PHP builtin web server.</info>', substr($event->getComposer()->getConfig()->get('bin-dir') . '/typo3cms', strlen(getcwd()) + 1)));
        }

        return true;
    }

    private function initializeCompatibilityLayer(ScriptEvent $event)
    {
        $composer = $event->getComposer();
        $package = $composer->getPackage();
        $generator = $composer->getAutoloadGenerator();
        $packages = $composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
        $packageMap = $generator->buildPackageMap($composer->getInstallationManager(), $package, $packages);
        $map = $generator->parseAutoloads($packageMap, $package);
        $loader = $generator->createLoader($map);
        $loader->register();
        Kernel::initializeCompatibilityLayer($loader);
    }
}
