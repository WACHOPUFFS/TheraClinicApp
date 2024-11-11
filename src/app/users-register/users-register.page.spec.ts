import { ComponentFixture, TestBed } from '@angular/core/testing';
import { UsersRegisterPage } from './users-register.page';

describe('UsersRegisterPage', () => {
  let component: UsersRegisterPage;
  let fixture: ComponentFixture<UsersRegisterPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(UsersRegisterPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
