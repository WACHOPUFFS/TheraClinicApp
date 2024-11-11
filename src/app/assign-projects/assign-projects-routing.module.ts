import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { AssignProjectsPage } from './assign-projects.page';

const routes: Routes = [
  {
    path: '',
    component: AssignProjectsPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class AssignProjectsPageRoutingModule {}
