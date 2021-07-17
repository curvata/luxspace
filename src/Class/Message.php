<?php

namespace App\Class;

class Message
{
    private string $mail;
    private mixed $content;
    private string $template;
    private string $subject;
        
    public function __construct(string $mail, string $subject, string $template, mixed $content)
    {
        $this->mail = $mail;
        $this->subject = $subject;
        $this->template = $template;
        $this->content = $content;
    }
    
    public function getMail(): ?string
    {
        return $this->mail;
    }
    
    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }
    
    public function getContent(): mixed
    {
        return $this->content;
    }
    
    public function setContent(mixed $content): self
    {
        $this->content = $content;

        return $this;
    }
    
    public function getTemplate(): ?string
    {
        return $this->template;
    }
    
    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }
    
    public function getSubject(): ?string
    {
        return $this->subject;
    }
    
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}
