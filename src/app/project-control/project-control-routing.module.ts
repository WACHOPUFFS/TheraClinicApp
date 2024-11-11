import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ProjectControlPage } from './project-control.page';

const routes: Routes = [
  {
    path: '',
    component: ProjectControlPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ProjectControlPageRoutingModule {}
