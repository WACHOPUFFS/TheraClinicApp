import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { AssignProjectsPageRoutingModule } from './assign-projects-routing.module';

import { AssignProjectsPage } from './assign-projects.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    AssignProjectsPageRoutingModule
  ],
  declarations: [AssignProjectsPage]
})
export class AssignProjectsPageModule {}
