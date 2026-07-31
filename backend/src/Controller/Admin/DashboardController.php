<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\Translation\t;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle(t('admin.dashboard.title'));
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard(t('admin.menu.dashboard'), 'fa fa-chart-line');
        yield MenuItem::linkTo(SportCrudController::class, t('admin.menu.sports'), 'fa fa-futbol');
        yield MenuItem::linkTo(RegionCrudController::class, t('admin.menu.regions'), 'fa fa-globe');
        yield MenuItem::linkTo(BookmakerCrudController::class, t('admin.menu.bookmakers'), 'fa fa-building');
        yield MenuItem::linkTo(TagCrudController::class, t('admin.menu.tags'), 'fa fa-tags');
        yield MenuItem::linkTo(CompetitionCrudController::class, t('admin.menu.competitions'), 'fa fa-trophy');
        yield MenuItem::linkTo(BetSlipCrudController::class, t('admin.menu.bet_slips'), 'fa fa-receipt');
        yield MenuItem::linkToRoute(t('admin.menu.api_health'), 'fa fa-heartbeat', 'api_health');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->disableLogoutLink()
            ->addMenuItems([
                MenuItem::linkToRoute(t('admin.menu.sign_out'), 'internal:sign-out', 'api_logout')
                    ->setCssClass('safe-post-logout'),
            ]);
    }
}
