import { Component, Input } from '@angular/core';
import { ModalController } from '@ionic/angular';

@Component({
  selector: 'app-modal-success',
  templateUrl: './modal-success.component.html',
  styleUrls: ['./modal-success.component.scss'],
})
export class ModalSuccessComponent {
  @Input() titulo: string;
  @Input() mensaje: string;

  constructor(private modalController: ModalController) {}

  dismiss() {
    this.modalController.dismiss();
  }

}
