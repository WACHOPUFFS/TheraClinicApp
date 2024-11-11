import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { RegisterExamplePageRoutingModule } from './register-example-routing.module';

import { RegisterExamplePage } from './register-example.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    RegisterExamplePageRoutingModule
  ],
  declarations: [RegisterExamplePage]
})
export class RegisterExamplePageModule {}
