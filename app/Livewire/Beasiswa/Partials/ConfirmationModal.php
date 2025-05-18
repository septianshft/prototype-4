<?php

namespace App\Livewire\Beasiswa\Partials;

use Livewire\Component;

class ConfirmationModal extends Component
{

    public $show = false;
    public $title = 'Konfirmasi';
    public $message = 'Apakah Anda yakin?';
    public $confirmText = 'Ya';
    public $cancelText = 'Batal';
    public $onConfirm;

    protected $listeners = ['showModal' => 'open'];

    public function open($params = [])
    {
        $this->show = true;
        $this->title = $params['title'] ?? $this->title;
        $this->message = $params['message'] ?? $this->message;
        $this->confirmText = $params['confirmText'] ?? $this->confirmText;
        $this->cancelText = $params['cancelText'] ?? $this->cancelText;
        $this->onConfirm = $params['onConfirm'] ?? null;
    }

    public function close()
    {
        $this->show = false;
    }

    public function confirm()
    {
        $this->dispatch($this->onConfirm);
        $this->close();
    }

    public function render()
    {
        return view('livewire.beasiswa.partials.confirmation-modal');
    }
}
