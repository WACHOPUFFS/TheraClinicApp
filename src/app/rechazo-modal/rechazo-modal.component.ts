import { Component, Input } from '@angular/core';
import { ModalController } from '@ionic/angular';

@Component({
  selector: 'app-rechazo-modal',
  templateUrl: './rechazo-modal.component.html',
  styleUrls: ['./rechazo-modal.component.scss'],
})
export class RechazoModalComponent {
  @Input() archivo: any;
  comentario: string = '';

  constructor(private modalController: ModalController) {}

  close() {
    this.modalController.dismiss();
  }

  rechazar() {
    this.modalController.dismiss({
      comentario: this.comentario
    });
  }
}
