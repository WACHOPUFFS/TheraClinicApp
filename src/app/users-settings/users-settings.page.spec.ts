import { ComponentFixture, TestBed } from '@angular/core/testing';
import { UsersSettingsPage } from './users-settings.page';

describe('UsersSettingsPage', () => {
  let component: UsersSettingsPage;
  let fixture: ComponentFixture<UsersSettingsPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(UsersSettingsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
