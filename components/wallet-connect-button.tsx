"use client";

import { useTonAddress, useTonConnectUI, useTonWallet } from "@tonconnect/ui-react";
import { Button } from "@/components/ui/button";
import { Wallet } from "lucide-react";

export function WalletConnectButton() {
  const wallet = useTonWallet();
  const [tonConnectUI] = useTonConnectUI();
  const userFriendlyAddress = useTonAddress();
  
  if (!wallet) {
    return (
      <Button 
        variant="outline" 
        onClick={() => tonConnectUI.openModal()}
      >
        <Wallet className="mr-2 h-4 w-4" />
        Connect Wallet
      </Button>
    );
  }
  
  return (
    <Button 
      variant="outline" 
      onClick={() => tonConnectUI.openModal()}
    >
      <Wallet className="mr-2 h-4 w-4" />
      {userFriendlyAddress.slice(0, 6)}...{userFriendlyAddress.slice(-4)}
    </Button>
  );
} 