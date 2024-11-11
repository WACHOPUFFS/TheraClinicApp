import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ProjectControlPageRoutingModule } from './project-control-routing.module';

import { ProjectControlPage } from './project-control.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ProjectControlPageRoutingModule
  ],
  declarations: [ProjectControlPage]
})
export class ProjectControlPageModule {}
