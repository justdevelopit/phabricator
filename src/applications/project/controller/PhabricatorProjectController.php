<?php

abstract class PhabricatorProjectController extends PhabricatorController {

  protected function buildLocalNavigation(PhabricatorProject $project) {
    $id = $project->getID();

    $nav_view = new AphrontSideNavFilterView();
    $uri = new PhutilURI('/project/view/'.$id.'/');
    $nav_view->setBaseURI($uri);

    $external_arrow = "\xE2\x86\x97";
    $tasks_uri = '/maniphest/view/all/?projects='.$project->getPHID();
    $slug = PhabricatorSlug::normalize($project->getPhrictionSlug());
    $phriction_uri = '/w/projects/'.$slug;

    $edit_uri = '/project/edit/'.$id.'/';
    $members_uri = '/project/members/'.$id.'/';

    $nav_view->addLabel(pht('Project'));
    $nav_view->addFilter('dashboard', pht('Dashboard'));
    $nav_view->addFilter(null, pht('Tasks').' '.$external_arrow, $tasks_uri);
    $nav_view->addFilter(null, pht('Wiki').' '.$external_arrow, $phriction_uri);

    $user = $this->getRequest()->getUser();
    $can_edit = PhabricatorPolicyCapability::CAN_EDIT;

    $nav_view->addLabel(pht('Manage'));
    if (PhabricatorPolicyFilter::hasCapability($user, $project, $can_edit)) {
      $nav_view->addFilter('edit', pht("Edit Project"), $edit_uri);
      $nav_view->addFilter('members', pht("Edit Members"), $members_uri);
    } else {
      $nav_view->addFilter(
        'edit',
        pht("Edit Project"),
        $edit_uri,
        $relative = false,
        'disabled');
      $nav_view->addFilter(
        'members',
        pht("Edit Members"),
        $members_uri,
        $relative = false,
        'disabled');
    }

    return $nav_view;
  }

  public function buildSideNavView($filter = null, $for_app = false) {
    $user = $this->getRequest()->getUser();

    $nav = new AphrontSideNavFilterView();
    $nav
      ->setBaseURI(new PhutilURI('/project/filter/'))
      ->addLabel(pht('User'))
      ->addFilter('active', pht('Active'))
      ->addLabel(pht('All'))
      ->addFilter('all', pht('All Projects'))
      ->addFilter('allactive', pht('Active Projects'))
      ->selectFilter($filter, 'active');

    if ($for_app) {
      $nav->addFilter('create/', pht('Create Project'));
    }

    return $nav;
  }

  public function buildApplicationCrumbs() {
    $crumbs = parent::buildApplicationCrumbs();

    $crumbs->addAction(
      id(new PhabricatorMenuItemView())
        ->setName(pht('Create Project'))
        ->setHref($this->getApplicationURI('create/'))
        ->setIcon('create'));

    return $crumbs;
  }

}
